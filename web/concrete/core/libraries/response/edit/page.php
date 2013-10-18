<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_PageEditResponse extends Response {

	public $cID;

	public function setPage(Page $page) {
		$this->cID = $page->getCollectionID();
	}

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();
		$o->cID = $this->cID;
		return $o;
	}
	

}