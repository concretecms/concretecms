<?php 
namespace Concrete\Core\Http;
use Cookie;

class Response extends \Symfony\Component\HttpFoundation\Response {

	public function send() {
		$cookies = Cookie::getCookies();
		foreach($cookies as $cookie) {
			$this->headers->setCookie($cookie);
		}
		parent::send();
	}
}