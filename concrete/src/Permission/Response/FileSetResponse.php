<?php
namespace Concrete\Core\Permission\Response;
use User;
use FileSet;
class FileSetResponse extends Response {

	public function canSearchFiles() { return $this->validate('search_file_set'); }
	public function canRead() { return $this->validate('view_file_set_file'); }
	public function canWrite() { return $this->validate('edit_file_set_file'); }
	public function canAddFiles() { return $this->validate('add_file');}
	public function canAccessFileManager() {return $this->validate('search_file_set'); }

	/**
	 * Returns all file extensions this user can add
	 */
	public function getAllowedFileExtensions() {
		$pk = $this->category->getPermissionKeyByHandle('add_file');
		$pk->setPermissionObject($this->object);
		$r = $pk->getAllowedFileExtensions();
		return $r;
	}

	public function canAddFileType($ext) {
		$pk = $this->category->getPermissionKeyByHandle('add_file');
		$pk->setPermissionObject($this->object);
		return $pk->validate($ext);
	}

	public function canDeleteFileSet() {
		$fs = $this->getPermissionObject();
		$u = new User();
		if ($fs->getFileSetType() == FileSet::TYPE_PRIVATE && $fs->getFileSetUserID() == $u->getUserID()) {
			return true;
		}
		return $this->validate('delete_file_set');
	}

}
