<?
defined('C5_EXECUTE') or die("Access Denied.");
class FileSetPermissionResponse extends PermissionResponse {
	
	public function camSearchFiles() { return $this->validate('search_file_set'); }

}