<?php
namespace Concrete\Core\User\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;

class AddUser extends AbstractEvent {

	protected $proceed = true;
    protected $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

	public function cancelAdd() {
		$this->proceed = false;
	}

	public function proceed() {
		return $this->proceed;
	}

}