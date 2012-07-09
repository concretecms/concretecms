<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Types_Composer extends Controller {

	protected $ct = false;
	
	protected function verify($ctID = false) {
		$cap = Loader::helper('concrete/dashboard');
		$ct = false;
		if ($ctID > 0) {
			$ct = CollectionType::getByID($ctID);
		}
		
		if ($cap->canAccessComposer() && is_object($ct)) {
			$this->set('ct', $ct);
			$this->ct = $ct;
		} else {
			$this->redirect("/dashboard/pages/types");
		}
	}
	
	public function save_content_items($ctID = false) {
		$this->verify($ctID);
		$items = array();
		foreach($this->post('item') as $item) {
			$obj = new stdClass;
			if (substr($item, 0, 3) == 'bID') {
				$obj->bID = substr($item, 3);
			} else {
				$obj->akID = substr($item, 4);
			}
			$items[] = $obj;
		}
		$this->ct->saveComposerContentItemOrder($items);
		exit;
	}
	
	public function view($ctID = false, $action = false) { 
		$this->verify($ctID);
		$this->set('contentitems', $this->ct->getComposerContentItems());
		if ($action == 'updated') {
			$this->set('message', t('Composer settings updated.'));
		}
	}	
	
	public function save() {
		$this->verify($this->post('ctID'));
		if ($this->post('ctIncludeInComposer')) {
			switch($this->post('ctComposerPublishPageMethod')) {
				case 'PARENT':
					$page = Page::getByID($this->post('ctComposerPublishPageParentID'));
					$this->ct->saveComposerPublishTargetPage($page);
					break;
				case 'PAGE_TYPE':
					$ct = CollectionType::getByID($this->post('ctComposerPublishPageTypeID'));
					$this->ct->saveComposerPublishTargetPageType($ct);
					break;
				default:
					$this->ct->saveComposerPublishTargetAll();					
					break;
			}
			$this->ct->saveComposerAttributeKeys($this->post('composerAKID'));
			$this->redirect('/dashboard/pages/types/composer', 'view', $this->ct->getCollectionTypeID(), 'updated');

		} else {
			$this->ct->resetComposerData();
			$this->redirect("/dashboard/pages/types", "clear_composer");
		}
	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	

}