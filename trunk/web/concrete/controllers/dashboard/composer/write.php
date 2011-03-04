<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('composer_page');
class DashboardComposerWriteController extends Controller {

	public $helpers = array('form', 'html');
	
	public function save() {
		if ($this->isPost()) {
			if (intval($this->post('entryID')) > 0) {
				$entry = ComposerPage::getByID($this->post('entryID'));
			}
			
			if (!is_object($entry)) {
				$this->error->add(t('Invalid page.'));
			}
		
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
				}
				
				$data = array('cName' => $this->post('cName'), 'cDescription' => $this->post('cDescription'));
				$entry->update($data);
				$this->saveData($entry);
				$entry->markComposerPageAsSaved();
				if ($this->post('ccm-submit-publish')) {
					$this->redirect('?cID=' . $p->getCollectionID());
				} else if ($this->post('autosave')) { 
					// this is done by javascript. we refresh silently and send a json success back
					$json = Loader::helper('json');
					$obj = new stdClass;
					$dh = Loader::helper('date');
					$obj->error = false;
					$obj->time = $dh->getLocalDateTime('now','g:i a');
					$obj->timestamp =date('m/d/Y g:i a');
					print $json->encode($obj);
					exit;
				} else {
					$this->redirect('/dashboard/composer/write', 'edit', $entry->getCollectionID(), 'saved');
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
			$entry = ComposerPage::getByID($cID);
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
			$this->set('blocks', $entry->getComposerBlocks());
		}
	}
	
	protected function saveData($p) {
		$ct = CollectionType::getByID($p->getCollectionTypeID());
		$blocks = $p->getComposerBlocks();
		
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
			// create a new page of this type
			$entry = ComposerPage::createDraft($ct);
			$this->redirect('/dashboard/composer/write', 'edit', $entry->getCollectionID());
		}
	}
	
}