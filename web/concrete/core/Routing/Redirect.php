<?php
namespace Concrete\Core\Routing;
use Loader;
use Request;
use URL as CoreUrl;
class Redirect {

	/** 
	 * Actually sends a redirect
	 */
	protected static function createRedirectResponse($url, $code, $headers) {
		$r = new RedirectResponse($url, $code, $headers);
		$r->setRequest(Request::getInstance());
		return $r;
	}

	/** 
	* Redirects to a concrete5 resource.	
	 */
	public static function to() {
		$url = BASE_URL . call_user_func_array(array('CoreUrl', 'to'), func_get_args());
		$r = static::createRedirectResponse($url, 302, array());
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
	* Redirects to a URL.	
	 */
	public static function url($url, $code = 302, $headers = array()) {
		$r = static::createRedirectResponse($url, $code, $headers);
		return $r;
	}	


}