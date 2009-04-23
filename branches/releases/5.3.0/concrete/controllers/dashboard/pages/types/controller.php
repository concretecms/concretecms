<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardPagesTypesController extends Controller {


public function view() { 
	
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
			$template_cID = $db->getOne("SELECT cID FROM Pages WHERE cIsTemplate = 1 and ctID = ?",array($ctID));
			
			if($template_cID) {
				$template = Page::getByID($template_cID);
				if($template->getCollectionID() > 1) {
					$template->delete();	
				}
			}
			
			$db->query("DELETE FROM PageTypes WHERE ctID = ?",array($ctID));
			$db->query("DELETE FROM PageTypeAttributes WHERE ctID = ?",array($ctID));
			$this->redirect("/dashboard/pages/types");
		} else {
			$this->set("message", t("You must delete all pages of this type before deleting this page type."));
		}
	}
}

} // end class def 
?>