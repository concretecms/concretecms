<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 

class Concrete5_Model_UserPointActionDescription {

	public function setComments($comments) {
		$this->comments = $comments;
	}

	public function getComments() {
		return $this->comments;
	}

	public function getUserPointActionDescription() {
		return $this->getComments();
	}


}