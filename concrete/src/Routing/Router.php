<?php
namespace Concrete\Core\Routing;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use \Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;
use Request;
use Loader;

class Router implements RouterInterface
{

    /**
     * @var UrlGeneratorInterface|null
     */
    protected $generator;

    /**
     * @var RequestContext|null
     */
    protected $context;

    /**
     * @var SymfonyRouteCollection
     */
	protected $collection;

	protected $request;
	protected $themePaths = array();
	public $routes = array();

	public function __construct() {
		$this->collection = new SymfonyRouteCollection();
	}

    /**
     * @return RequestContext
     */
    public function getContext()
    {
        if (!$this->context) {
            $this->context = new RequestContext;
            $this->context->fromRequest(\Request::getInstance());
        }
        return $this->context;
    }

    /**
     * @param RequestContext $context
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return UrlGeneratorInterface
     */
    public function getGenerator()
    {
        if (!$this->generator) {
            $this->generator = new UrlGenerator($this->getList(), $this->getContext());
        }
        return $this->generator;
    }

    /**
     * @param $generator
     */
    public function setGenerator(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

	public function getList() {
		return $this->collection;
	}

	public function setRequest(Request $req) {
		$this->request = $req;
	}

	/**
	 * Register a symfony route with as little as a path and a callback.
	 *
	 * @param string $path The full path for the route
	 * @param \Closure|string $callback `\Closure` or "dispatcher" or "\Namespace\Controller::action_method"
	 * @param string|null $handle The route handle, if one is not provided the handle is generated from the path "/" => "_"
	 * @param array $requirements The Parameter requirements, see Symfony Route constructor
	 * @param array $options The route options, see Symfony Route constructor
	 * @param string $host The host pattern this route requires, see Symfony Route constructor
	 * @param array|string $schemes The schemes or scheme this route requires, see Symfony Route constructor
	 * @param array|string $methods The HTTP methods this route requires, see see Symfony Route constructor
	 * @param string $condition see Symfony Route constructor
	 * @return \Symfony\Component\Routing\Route
	 */
    public function register(
		$path,
		$callback,
		$handle = null,
		array $requirements = array(),
		array $options = array(),
		$host = '',
		$schemes = array(),
		$methods = array(),
		$condition = null)
	{
		// setup up standard concrete5 routing.
		$trimmed_path = trim($path, '/');
		if (!$handle) {
			$handle = preg_replace('/[^A-Za-z0-9\_]/', '_', $trimmed_path);
			$handle = preg_replace('/\_+/', '_', $handle);
			$handle = trim($handle, '_');
		}
		$path = '/' . $trimmed_path . '/';

		if ($callback instanceof \Closure) {
			$attributes = ClosureRouteCallback::getRouteAttributes($callback);
		} else if ($callback == 'dispatcher') {
			$attributes = DispatcherRouteCallback::getRouteAttributes($callback);
		} else {
			$attributes = ControllerRouteCallback::getRouteAttributes($callback);
		}
		$attributes['path'] = $path;

		$route = new Route($path, $attributes, $requirements, $options, $host, $schemes, $methods, $condition);
		$this->collection->add($handle, $route);

		return $route;
	}

    public function registerMultiple(array $routes)
    {
        foreach ($routes as $route => $route_settings) {
            array_unshift($route_settings, $route);
            call_user_func_array(array($this, 'register'), $route_settings);
        }
    }

	public function execute(Route $route, $parameters) {
		$callback = $route->getCallback();
		$response = $callback->execute($this->request, $route, $parameters);
		return $response;
	}

	/**
	 * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
	 * @access public
	 * @param $path string
	 * @param $theme object, if null site theme is default
	 * @return void
	*/
	public function setThemeByRoute($path, $theme = NULL, $wrapper = FILENAME_THEMES_VIEW) {
		$this->themePaths[$path] = array($theme, $wrapper);
	}

    public function setThemesbyRoutes(array $routes)
    {
        foreach($routes as $route => $theme) {
			if (is_array($theme)) {
	            $this->setThemeByRoute($route, $theme[0], $theme[1]);
			} else {
				$this->setThemeByRoute($route, $theme);
			}
        }
    }

	/**
	 * This grabs the theme for a particular path, if one exists in the themePaths array
	 * @param string $path
	 * @return string|boolean
	*/
	public function getThemeByRoute($path) {
		// there's probably a more efficient way to do this
		$txt = Loader::helper('text');
		foreach ($this->themePaths as $lp => $layout) {
			if ($txt->fnmatch($lp, $path)) {
				return $layout;
			}
		}
		return false;
	}

    public function route($data)
    {
        if (is_array($data)) {
            $path = $data[0];
            $pkg = $data[1];
        } else {
            $path = $data;
        }

        $path = trim($path, '/');
        $pkgHandle = null;
        if ($pkg) {
            if (is_object($pkg)) {
                $pkgHandle = $pkg->getPackageHandle();
            } else {
                $pkgHandle = $pkg;
            }
        }

        $route = '/ccm';
        if ($pkgHandle) {
            $route .= "/{$pkgHandle}";
        } else {
            $route .= '/system';
        }

        $route .= "/{$path}";
        return $route;
    }

}
