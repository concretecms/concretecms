<?php 
namespace Concrete\Core\Http;
use Cookie;

class Response extends \Symfony\Component\HttpFoundation\Response {

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