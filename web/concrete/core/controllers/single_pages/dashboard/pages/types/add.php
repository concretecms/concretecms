<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Types_Add extends DashboardBaseController {
	
	
	public function on_start() { 
		parent::on_start();
		$this->set("icons", CollectionType::getIcons());
	}	
	
	
	public function do_add() {
		$ctName = $_POST['ctName'];
		$ctHandle = $_POST['ctHandle'];
		$vs = Loader::helper('validation/strings');
		
		$error = array();
		if (!$ctHandle) {
			$this->error->add(t("Handle required."));
		} else if (!$vs->handle($ctHandle)) {
			$this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
		}

		if (CollectionType::getByHandle($ctHandle)) {
			$this->error->add(t('Handle already exists.'));
		}
		
		if (!$ctName) {
			$this->error->add(t("Name required."));
		} else if (preg_match('/[<>{};?"`]/i', $ctName)) {
			$this->error->add(t('Invalid characters in page type name.'));
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
