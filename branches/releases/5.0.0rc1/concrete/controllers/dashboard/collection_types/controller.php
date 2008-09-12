<?php 
class DashboardCollectionTypesController extends Controller {


public function view() {

}

public function delete($ctID) {
	$db = Loader::db();

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
		$this->redirect("/dashboard/collection_types");
	} else {
		$this->set("message","The page type could not be deleted because pages of that type exist in the system.<br/>You must delete all pages of this type before deleting this page type.");
	}
	

}

} // end class def 
?>