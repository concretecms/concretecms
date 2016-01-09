<?php 
namespace Concrete\Core\Http;
use Cookie;
use Config;
use Core;

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
		
		if ($this->headers->has('X-Frame-Options') === false) {
			$x_frame_options = Config::get('concrete.security.misc.x_frame_options');
			if (Core::make('helper/validation/strings')->notempty($x_frame_options)) {
				$this->headers->set('X-Frame-Options', $x_frame_options);
			}
		}
		
		parent::send();
	}
}