<?php
namespace Concrete\Core\Page;
use \Symfony\Component\EventDispatcher\GenericEvent;
use User;

class Event extends GenericEvent {

	protected $page;
	protected $user;
	protected $request;

	public function __construct(Page $c) {
		$this->page = $c;
	}

	public function setUser(User $u) {
		$this->user = $u;
	}

	public function setRequest($request)
	{
		$this->request = $request;
	}

	/** @return \Symfony\Component\HttpFoundation\Request */
	public function getRequest()
	{
		return $this->request;
	}

	public function getPageObject() {
		return $this->page;
	}

	public function getUserObject() {
		return $this->user;
	}
}
