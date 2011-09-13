<?

Loader::controller('/dashboard/base');
class DashboardSettingsAttributeSetsController extends DashboardBaseController {
	
	public $helpers = array('form');
	protected $category;
	
	public function view($categoryID = false, $mode = false) {
		$this->category = AttributeKeyCategory::getByID($categoryID);
		if (is_object($this->category)) {
			$sets = $this->category->getAttributeSets();
			$this->set('sets', $sets);
		} else {
			$this->redirect('/dashboard/settings');
		}
		$this->set('categoryID', $categoryID);
		switch($mode) {
			case 'set_added':
				$this->set('message', t('Attribute set added.'));
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
				
				$this->category->addSet($this->post('asHandle'), $this->post('asName'));
				$this->redirect('dashboard/settings/attribute_sets', $this->category->getAttributeKeyCategoryID(), 'set_added');
			}
			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	
}