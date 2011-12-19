<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
Loader::model('single_page');
Loader::model('collection_attributes');
class DashboardPagesTypesController extends Controller {
	
	
	public function view() { 
		$this->set("icons", CollectionType::getIcons());
	}	
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function delete($ctID, $token = '') {
		$db = Loader::db();
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_page_type', $token)) {
			$this->set('message', $valt->getErrorMessage());
		} else {
			// check to make sure we 
			$pageCount = $db->getOne("SELECT COUNT(*) FROM Pages WHERE cIsTemplate = 0 and ctID = ?",array($ctID));
				
			if($pageCount == 0) {
				$ct = CollectionType::getByID($ctID);
				$ct->delete();
				$this->redirect("/dashboard/pages/types");
			} else {
				$this->set("error", array(t("You must delete all pages of this type before deleting this page type.")));
			}
		}
	}
	
	public function page_type_added() {
		$this->set('message', t('Page type added successfully.'));
		$this->view();
	}

	public function page_type_updated() {
		$this->set('message', t('Page type updated successfully.'));
		$this->view();
	}
	
	public function clear_composer() {
		$this->set('message', t("This page type is no longer included in composer."));
	}
	
	public function update() {
	
	
	}

}