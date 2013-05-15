<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Types extends DashboardBaseController {
	
	public function view() { 
		$this->set("icons", CollectionType::getIcons());
	}	
	
	public function delete($ctID, $token = '') {
		$db = Loader::db();
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_page_type', $token)) {
			$this->set('message', $valt->getErrorMessage());
		} else {
			// check to make sure we 
			$pageCount = $db->getOne("SELECT COUNT(p.cID) from Pages p inner join CollectionVersions cv on p.cID = cv.cID WHERE cIsTemplate = 0 and cv.ctID = ?",array($ctID));
				
			if($pageCount == 0) {
				$ct = CollectionType::getByID($ctID);
				$ct->delete();
				$this->redirect("/dashboard/pages/types");
			} else {
				$this->error->add(t("You must delete all pages of this type, and remove all page versions that contain this page type before deleting this page type."));
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
		
		$valt = Loader::helper('validation/token');

		$ct = CollectionType::getByID($_REQUEST['ctID']);
		$ctName = $_POST['ctName'];
		$ctHandle = $_POST['ctHandle'];
		$vs = Loader::helper('validation/strings');
		
		if (!$ctHandle) {
			$this->error->add(t("Handle required."));
		} else if (!$vs->handle($ctHandle)) {
			$this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
		}
		
		if (!$ctName) {
			$this->error->add(t("Name required."));
		} else if (preg_match('/[<>;{}?"`]/i', $ctName)) {
			$this->error->add(t('Invalid characters in page type name.'));
		}
		
		
		if (!$valt->validate('update_page_type')) {
			$this->error->add($valt->getErrorMessage());
		}
		
		if (!$this->error->has()) {
			try {
				if (is_object($ct)) {
					$ct->update($_POST);
					$this->redirect('/dashboard/pages/types', 'page_type_updated');
				}		
			} catch(Exception $e1) {
				$this->error->add($e1->getMessage());
			}
		}	

		$this->view();

	
	}

}