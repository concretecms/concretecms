<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardComposerWriteController extends Controller {

	public $helpers = array('form', 'html');
	
	public function save() {
		$ct = $this->setCollectionType($this->post('ctID'));
		$this->set('action', 'add');			
		
		if ($this->isPost()) {
			$valt = Loader::helper('validation/token');
			$vtex = Loader::helper('validation/strings');
			
			if (!$valt->validate('composer')) {
				$this->error->add($valt->getErrorMessage());
			}
			
			if ($this->post("ccm-submit-publish")) {
				if (!$vtex->notempty($this->post('cName'))) {
					$this->error->add(t('You must provide a name for your page before you can publish it.'));
				}
			}
			
			if (!$this->error->has()) {
				$parent = false;
				if ($this->post('ccm-submit-publish')) {
					switch($ct->getCollectionTypeComposerPublishMethod()) {
						case 'PARENT':
							$parent = Page::getByID($ct->getCollectionTypeComposerPublishPageParentID());
							break;
						default:
							$parent = Page::getByID($this->post('cParentID'));
							break;
					}
				} else {
					$parent = Page::getCurrentPage();
				}
				
				$data = array('cName' => $this->post('cName'), 'cDescription' => $this->post('cDescription'));
				$p = $parent->add($ct, $data);
				$this->saveData($p);
				if ($this->post('ccm-submit-publish')) {
					$this->redirect('?cID=' . $p->getCollectionID());
				} else {
					$this->redirect('/dashboard/composer/write', 'edit', $p->getCollectionID(), 'saved');
				}
			}
			
		} else {
			$this->redirect('/dashboard/composer');	
		}
	}
	
	public function edit($cID = false, $mode = false) {
		
		switch($mode) {
			case 'saved':
				$this->set('message', t('Draft saved successfully.'));
				break;
		}
		
		if (intval($cID) > 0) {
			$entry = Page::getByID($cID);
			if (is_object($entry) && !$entry->isError()) {
				if (!CollectionType::isValidComposerDraft($entry)) {
					unset($entry);
				}
			}
		}
		
		if (!is_object($entry)) {
			$this->redirect("/dashboard/composer");
		} else {
			$ct = CollectionType::getByID($entry->getCollectionTypeID());
			$this->set('entry', $entry);
			$this->set("ct", $ct);
			$this->set('name', $entry->getCollectionName());
			$this->set('description', $entry->getCollectionDescription());
			$this->set('attribs', $ct->getComposerAttributeKeys());
			$this->set('blocks', $ct->getCollectionTypeComposerBlocks());
		}
	}
	
	protected function saveData($p) {
		$ct = $this->get('ct');
		$blocks = $ct->getCollectionTypeComposerBlocks();
		// now we grab the instance on the created page
		foreach($blocks as $b) {
			$req = $b->getController()->post();
			$b2 = Block::getByID($b->getBlockID(), $p, $b->getAreaHandle());
			if ($b2->isAlias()) {
				$nb = $b2->duplicate($p);
				$b2->deleteBlock();
				$b2 = $nb;
			}
					
			// we can update the block that we're submitting
			$b2->update($req);
		}					
				
		Loader::model("attribute/categories/collection");
		$aks = $ct->getComposerAttributeKeys();
		foreach($aks as $cak) {
			$cak->saveAttributeForm($p);				
		}	
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->set('disableThirdLevelNav', true);
	}
	
	protected function setCollectionType($ctID) {
		if (intval($ctID) > 0) {
			$ct = CollectionType::getByID($ctID);
			if (is_object($ct)) {
				if (!$ct->isCollectionTypeIncludedInComposer()) {
					unset($ct);
				}
			}			
		}
		if (is_object($ct)) {
			$this->set('ct', $ct);
			$this->set('attribs', $ct->getComposerAttributeKeys());
			$this->set('blocks', $ct->getCollectionTypeComposerBlocks());
		}
		return $ct;
	}

	public function view($ctID = false) {
		$ct = $this->setCollectionType($ctID);		
		if (!is_object($ct)) {
			$ctArray = CollectionType::getComposerPageTypes();
			if (count($ctArray) == 1) {
				$ct = $ctArray[0];
				$this->redirect('/dashboard/composer/write', $ct->getCollectionTypeID());
				exit;
			}
			$this->set('ctArray', $ctArray);
			//$this->redirect('/dashboard/composer');
		} else {
			$this->set('action', 'add');			
		}
	}
	
}