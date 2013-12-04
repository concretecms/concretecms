<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_FileEditResponse extends EditResponse {

	public function setFile(File $file) {
		$this->files[] = $file;
	}

	public function setFiles($files) {
		$this->files = $files;
	}

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();
		foreach($this->files as $file) {
			$o->files[] = $file->getJSONObject();
		}
		return $o;
	}
	

}