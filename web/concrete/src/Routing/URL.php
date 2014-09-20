<?php
namespace Concrete\Core\Routing;
use Config;
use Loader;
use Page;
use Concrete\Core\Routing\Router;

class URL {

	public static function isValidURL($path) {
		return filter_var($path, FILTER_VALIDATE_URL);
	}

	public static function page(Page $c, $action = false) {
		$args = func_get_args();
		$args[0] = Loader::helper('text')->encodePath($c->getCollectionPath());
		return call_user_func_array(array('\Concrete\Core\Routing\URL', 'to'), $args);
	}

	public static function to($path, $action = false) {
		if (static::isValidURL($path)) {
			return $path;
		}

		$dispatcher = '';
		if (!Config::get('concrete.seo.url_rewriting_all')) {
			$dispatcher = '/' . DISPATCHER_FILENAME;
		}

		$path = trim($path, '/');

		// if a query string appears in this variable, then we just pass it through as is
		if (strpos($path, '?') > -1) {
			return DIR_REL . $dispatcher. '/' . $path;
		} else {
			$_path = DIR_REL . $dispatcher. '/' . $path;
			if ($path) {
				$_path .= '/';
			}
		}

		if ($action != null) {
			$_path .= $action;
			$args = func_get_args();
			if (count($args) > 2) {
				for ($i = 2; $i < count($args); $i++){
					$_path .= '/' . $args[$i];
				}
			}

			if (strpos($_path, '?') === false) {
				$_path .= '/';
			}
		}

		if (!Config::get('concrete.seo.trailing_slash')) {
			$_path = rtrim($_path, '/');
		}
		return $_path;

	}

    /**
     * Returns a path to a route
     */
    public static function route($data)
    {
        $arguments = array_slice(func_get_args(), 1);
        if (!$arguments) {
            $arguments = array();
        }
        $route = Router::route($data);
        array_unshift($arguments, $route);
        return call_user_func_array(array('\Concrete\Core\Routing\URL', 'to'), $arguments);
    }

}
