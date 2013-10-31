<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse {

	protected $request;

	public function setRequest(Request $r) {
		$this->request = $r;
	}



}