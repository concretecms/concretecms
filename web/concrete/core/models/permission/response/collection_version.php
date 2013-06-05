<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_CollectionVersionPermissionResponse extends PermissionResponse {

	public function testForErrors() { 
		if (!$this->object->getVersionID()) {
			$c = Page::getByID($this->object->getCollectionID());
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) { 
				return COLLECTION_FORBIDDEN;
			} else { 
				return COLLECTION_NOT_FOUND;
			}
		} else if (!$this->object->isMostRecent()) {
			return VERSION_NOT_RECENT;
		}
	}

}