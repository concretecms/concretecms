<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_Redirect {

	/** 
	 * Actually sends a redirect
	 */
	protected static function createRedirectResponse($path, $code, $headers) {
		$r = new RedirectResponse($path, $code, $headers);
		$r->setRequest(Request::getInstance());
		return $r;
	}

	/** 
	 * Send a basic redirect
	 */
	public static function go($path, $code = 302, $headers = array()) {
		$r = static::createRedirectResponse($path, $code, $headers);
		return $r;
	}


	/** 
	 * Creates a basic redirect and executes it immediately.
	 */
	public static function send($path, $code = 302, $headers = array()) {
		$r = static::createRedirectResponse(URL::to($path), $code, $headers);
		$r->send();
	}

}