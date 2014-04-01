<?php
namespace Concrete\Core\File;
use \Concrete\Core\Application\EditResponse;
class FileEditResponse extends EditResponse {

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