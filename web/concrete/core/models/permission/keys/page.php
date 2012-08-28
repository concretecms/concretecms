<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PagePermissionKey extends PermissionKey {

	protected $multiplePageArray; // bulk operations
	public function setMultiplePageArray($pages) {
		$this->multiplePageArray = $pages;
	}
	public function getMultiplePageArray() {
		return $this->multiplePageArray;
	}

}
