<?

Loader::controller('/dashboard/base');
class DashboardSystemAttributeSetsController extends DashboardBaseController {
	
	public $helpers = array('form');
	protected $category;
	
	public function view($categoryID = false, $mode = false) {
		$this->category = AttributeKeyCategory::getByID($categoryID);
		if (is_object($this->category)) {
			$sets = $this->category->getAttributeSets();
			$this->set('sets', $sets);
		} else {
			$this->redirect('/dashboard/system');
		}
		$this->set('categoryID', $categoryID);
		switch($mode) {
			case 'set_added':
				$this->set('message', t('Attribute set added.'));
				break;
			case 'set_deleted':
				$this->set('message', t('Attribute set deleted.'));
				break;
			case 'set_updated':
				$this->set('message', t('Attribute set updated.'));
				break;
		}
	}

	public function add_set() {
		$this->view($this->post('categoryID'));
		if (Loader::helper('validation/token')->validate('add_set')) { 
			if (!trim($this->post('asHandle'))) { 
				$this->error->add(t("Specify a handle for your attribute set."));
			} else {
				$as = AttributeSet::getByHandle($this->post('asHandle'));
				if (is_object($as)) {
					$this->error->add(t('That handle is in use.'));
				}
			}
			if (!trim($this->post('asName'))) { 
				$this->error->add(t("Specify a name for your attribute set."));
			}
			if (!$this->error->has()) {
				if (!$this->category->allowAttributeSets()) {
					$this->category->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_SINGLE);
				}
				
				$this->category->addSet($this->post('asHandle'), $this->post('asName'), false, 0);
				$this->redirect('dashboard/system/attribute_sets', $this->category->getAttributeKeyCategoryID(), 'set_added');
			}
			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	
	public function update_set() {
		if (Loader::helper('validation/token')->validate('update_set')) { 
			$as = AttributeSet::getByID($this->post('asID'));
			if (!is_object($as)) {
				$this->error->add(t('Invalid attribute set.'));
			} else {
				if (!trim($this->post('asHandle')) && (!$as->isAttributeSetLocked())) { 
					$this->error->add(t("Specify a handle for your attribute set."));
				} else {
					$asx = AttributeSet::getByHandle($this->post('asHandle'));
					if (is_object($asx) && $asx->getAttributeSetID() != $as->getAttributeSetID()) {
						$this->error->add(t('That handle is in use.'));
					}
				}
				if (!trim($this->post('asName'))) { 
					$this->error->add(t("Specify a name for your attribute set."));
				}
			}
			
			if (!$this->error->has()) {
				if (!$as->isAttributeSetLocked()) {
					$as->updateAttributeSetHandle($this->post('asHandle'));
				}
				$as->updateAttributeSetName($this->post('asName'));
				$this->redirect('dashboard/system/attribute_sets', $as->getAttributeSetKeyCategoryID(), 'set_updated');
			}
			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	
	public function update_set_attributes() {
		if (Loader::helper('validation/token')->validate('update_set_attributes')) { 
			$as = AttributeSet::getByID($this->post('asID'));
			if (!is_object($as)) {
				$this->error->add(t('Invalid attribute set.'));
			}

			if (!$this->error->has()) {
				// go through and add all the attributes that aren't in another set
				$as->clearAttributeKeys();
				$cat = AttributeKeyCategory::getByID($as->getAttributeSetKeyCategoryID());
				$unassigned = $cat->getUnassignedAttributeKeys();			
				if (is_array($this->post('akID'))) {
					foreach($unassigned as $ak) { 
						if (in_array($ak->getAttributeKeyID(), $this->post('akID'))) {
							$as->addKey($ak);
						}
					}
				}
				$this->redirect('dashboard/system/attribute_sets', $cat->getAttributeKeyCategoryID(), 'set_updated');
			}	
			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->edit($this->post('asID'));
	}
	
	public function delete_set() {
		if (Loader::helper('validation/token')->validate('delete_set')) { 
			$as = AttributeSet::getByID($this->post('asID'));
			if (!is_object($as)) {
				$this->error->add(t('Invalid attribute set.'));
			} else if ($as->isAttributeSetLocked()) { 
				$this->error->add(t('This attribute set is locked. That means it was added by a package and cannot be manually removed.'));
				$this->edit($as->getAttributeSetID());
			}
			if (!$this->error->has()) {
				$categoryID = $as->getAttributeSetKeyCategoryID();
				$as->delete();
				$this->redirect('dashboard/system/attribute_sets', $categoryID, 'set_deleted');
			}			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	

	
	public function edit($asID = false) {
		$as = AttributeSet::getByID($asID);
		if (is_object($as)) {
			$this->set('set', $as);
		} else {
			$this->redirect('/dashboard/system');
		}
	}
	
}