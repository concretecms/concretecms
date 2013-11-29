<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_FileEditResponse extends EditResponse {

	public $fID;

	public function setFile(File $file) {
		$this->fID = $file->getFileID();
	}

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();
		$o->fID = $this->fID;
		return $o;
	}
	

}