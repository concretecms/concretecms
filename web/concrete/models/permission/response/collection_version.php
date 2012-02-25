<?
defined('C5_EXECUTE') or die("Access Denied.");
class CollectionVersionPermissionResponse extends PermissionResponse {

	public function testForErrors() { 
		if (!$this->object->getVersionID()) {
			$c = Page::getByID($this->object->getCollectionID());
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) { 
				$this->error = COLLECTION_FORBIDDEN;
			} else { 
				$this->error = COLLECTION_NOT_FOUND;
			}
		} else if (!$this->object->isMostRecent()) {
			$this->error = VERSION_NOT_RECENT;
		}
	}

}