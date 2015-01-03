<?php
namespace Concrete\Core\Routing;
use Core;
use Loader;
use Request;
use Page;

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
		$url = BASE_URL . call_user_func_array('\Concrete\Core\Routing\URL::to', func_get_args());
		$r = static::createRedirectResponse($url, 302, array());
		return $r;
	}

	/** 
	 * Redirect to a page
	 */
	public static function page(Page $c, $code = 302, $headers = array()) {
        if ($c->getCollectionPath()) {
            $url = Core::make('helper/navigation')->getLinkToCollection($c, true);
        } else {
            $url = BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID();
        }
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