<?php
namespace Concrete\Core\Permission\Key;
use Loader;
use User;
use \Concrete\Core\Permission\Duration as PermissionDuration;
class AddFileFileSetKey extends FileSetKey  {

	public function getAllowedFileExtensions() {
		$u = new User();

		$extensions = array();
		if ($u->isSuperUser()) {
			$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();
			return $extensions;
		}

		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}
	
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(FileSetKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		foreach($list as $l) {
			if ($l->getFileTypesAllowedPermission() == 'N') {
				$extensions = array();
			}
			if ($l->getFileTypesAllowedPermission() == 'C') {
				$extensions = array_unique(array_merge($extensions, $l->getFileTypesAllowedArray()));
			}
			if ($l->getFileTypesAllowedPermission() == 'A') {
				$extensions = Loader::helper('concrete/file')->getAllowedFileExtensions();
			}
		}
		
		return $extensions;
	}
	
	public function validate($extension = false) {
		$extensions = $this->getAllowedFileExtensions();
		if ($ext != false) {
			return in_array($extension, $extensions);
		} else {
			return count($extensions) > 0;
		}
	}
	

}