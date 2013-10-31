<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Response extends \Symfony\Component\HttpFoundation\Response {

	public function send() {
		// First, we see if we have any cookies to send along
		$cl = Cookie::getInstance();
		$cookies = $cl->getCookies();
		foreach($cookies as $cookie) {
			$this->headers->setCookie($cookie);
		}
		parent::send();
	}
}