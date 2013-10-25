<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_Redirect {

	/** 
	 * Actually sends a redirect
	 */
	protected static function createRedirectResponse($url, $code, $headers) {
		$r = new RedirectResponse($url, $code, $headers);
		$r->setRequest(Request::getInstance());
		return $r;
	}

	/** 
	 * Send a basic redirect
	 */
	public static function go($url, $code = 302, $headers = array()) {
		if (!URL::isValidURL($url)) {
			$url = URL::to($url);
		}
		$r = static::createRedirectResponse($url, $code, $headers);
		return $r;
	}

	/** 
	 * Redirect to a page
	 */
	public static function page(Page $c, $code = 302, $headers = array()) {
		$url = BASE_URL . URL::to($c->getCollectionPath());
		$r = static::createRedirectResponse($url, $code, $headers);
		return $r;
	}


	/** 
	 * Creates a basic redirect and executes it immediately.
	 */
	public static function send($path, $code = 302, $headers = array()) {
		$r = Redirect::go($path, $code, $headers);
		$r->send();
	}

}