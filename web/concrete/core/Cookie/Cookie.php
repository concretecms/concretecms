<?php

namespace Concrete\Core\Cookie;
use Symfony\Component\HttpFoundation\Cookie as CookieObject;
use Request;

class Cookie {

	static $pantry;
	protected $cookies = array();

	public static function getInstance() {
		if (null === self::$pantry) {
			self::$pantry = new Cookie();
		}
		return self::$pantry;
	}

	public static function set($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true) {
		// expiration in minutes
		$cl = Cookie::getInstance();
		$expire = ($expire > 0) ? $expire * 60 : 0;
		$cookie = new CookieObject($name, $value, $expire, $path, $domain, $secure, $httpOnly);
		$cl->add($cookie);
	}

	public function add($cookie) {
		$this->cookies[] = $cookie;
	}

	public static function get($name, $fullObject = false) {
		$request = Request::getInstance();
		$value = $request->cookies->get($name);
		return $value;
	}

	public function getCookies() {
		return $this->cookies;
	}
}