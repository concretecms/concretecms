<?php 
namespace Concrete\Core\Http;
use Cookie;

class Response extends \Symfony\Component\HttpFoundation\Response {

	public function send() {
        $cleared = Cookie::getClearedCookies();
        foreach($cleared as $cookie) {
            $this->headers->clearCookie($cookie);
        }
		$cookies = Cookie::getCookies();
		foreach($cookies as $cookie) {
			$this->headers->setCookie($cookie);
		}
		parent::send();
	}
}