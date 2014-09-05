<?php
namespace Concrete\Core\User\Group;

class DeleteEvent extends Event {

	protected $proceed = true;

	public function cancelDelete() {
		$this->proceed = false;
	}

	public function proceed() {
		return $this->proceed;
	}

}
