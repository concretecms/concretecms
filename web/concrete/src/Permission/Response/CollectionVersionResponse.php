<?
namespace Concrete\Core\Permission\Response;

use Page;
use Permissions;

class CollectionVersionResponse extends Response {

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
        return parent::testForErrors();
	}

}