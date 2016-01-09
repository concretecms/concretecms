<?php
namespace Concrete\Core\User\Point\Action;
class ActionDescription {

	public function setComments($comments)
	{
		$this->comments = $comments;
	}

	public function getComments()
	{
		return $this->comments;
	}

	public function getUserPointActionDescription()
	{
		return $this->getComments();
	}


}
