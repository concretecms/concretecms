<?php
namespace Concrete\Core\Page;
use User;

class DuplicatePageEvent extends Event {

	protected $newPage;

	public function setNewPageObject($newPage) {
		$this->newPage = $newPage;
	}

	public function getNewPageObject() {
		return $this->newPage;
	}

}
