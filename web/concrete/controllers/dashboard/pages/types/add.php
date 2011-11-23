<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('single_page');
Loader::model('collection_attributes');
class DashboardPagesTypesAddController extends DashboardBaseController {
	
	
	public function on_start() { 
		parent::on_start();
		$this->set("icons", CollectionType::getIcons());
	}	
	
	
	public function do_add() {
		$ctName = $_POST['ctName'];
		$ctHandle = $_POST['ctHandle'];
		
		$error = array();
		if (!$ctHandle) {
			$this->error->add(t("Handle required."));
		}
		if (!$ctName) {
			$this->error->add(t("Name required."));
		}
		
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('add_page_type')) {
			$this->error->add($valt->getErrorMessage());
		}
		
		$akIDArray = $_POST['akID'];
		if (!is_array($akIDArray)) {
			$akIDArray = array();
		}
		
		if (!$this->error->has()) { 
			try {
				if ($_POST['task'] == 'add') {
					$nCT = CollectionType::add($_POST);
					$this->redirect('/dashboard/pages/types', 'page_type_added');
				}		
				exit;
			} catch(Exception $e1) {
				$this->error->add($e1->getMessage());
			}
		}
	}

}