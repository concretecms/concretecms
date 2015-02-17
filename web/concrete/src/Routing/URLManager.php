<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Routing\URL\StandardResolver;
use Concrete\Core\Routing\URL\URLResolverInterface;

/**
 * Class URLManager
 *
 * @package Concrete\Core\Routing
 */
class URLManager implements URLResolverInterface
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $items = array();

    /**
     * @var string
     */
    protected $default;

    /**
     * @param Application $app
     * @param string      $default
     */
    public function __construct(Application $app, $default = 'concrete')
    {
        $this->app = $app;
        $this->default = $default;

        $this->items['concrete'] = new StandardResolver();
    }

    public function addResolver($handle, $resolver)
    {
        if (is_callable($resolver)) {
            $this->items[$handle] = call_user_func($resolver, $this->app);
        } elseif (is_string($resolver)) {
            $this->items[$handle] = new $resolver;
        } elseif ($resolver instanceof URLResolverInterface) {
            $this->items[$handle] = $resolver;
        }
    }

    /**
     * @param null $handle
     * @return URLResolverInterface
     * @throws \Exception
     */
    public function getResolver($handle = null)
    {
        if (!$handle) {
            return $this->getDefaultResolver();
        }
        if (!$this->hasResolver($handle)) {
            throw new \Exception(sprintf("Invalid URL Resolver '%s'", $handle));
        }

        return $this->items[$handle];
    }

    /**
     * @return URLResolverInterface|null
     * @throws \Exception
     */
    public function getDefaultResolver()
    {
        if ($this->default) {
            return $this->getResolver($this->default);
        }

        return null;
    }

    public function hasResolver($handle)
    {
        return isset($this->items[$handle]);
    }

    public function __call($method, $arguments)
    {
        $resolver = $this->getDefaultResolver();
        if (!method_exists($resolver, $method)) {
            dd($resolver);
            throw new \Exception('Invalid Method.');
        }

        $args = count($arguments);
        switch ($args) {
            case 0:
                return $resolver->{$method}();
            case 1:
                return $resolver->{$method}(array_shift($arguments));
            case 2:
                return $resolver->{$method}(
                    array_shift($arguments),
                    array_shift($arguments));
            case 3:
                return $resolver->{$method}(
                    array_shift($arguments),
                    array_shift($arguments),
                    array_shift($arguments));
            case 4:
                return $resolver->{$method}(
                    array_shift($arguments),
                    array_shift($arguments),
                    array_shift($arguments),
                    array_shift($arguments));
        }

        return call_user_func_array(array($resolver, $method), (array)$arguments);
    }

    public function setDefaultResolver($handle)
    {
        $this->default = $handle;
    }

    /**
     * Get the base url
     *
     * @return string
     */
    public function getBaseURL()
    {
        return $this->getDefaultResolver()->getBaseURL();
    }

    /**
     * Get the directory that concrete5 is relative to
     *
     * @return string
     */
    public function getRelativeDirectory()
    {
        return $this->getDefaultResolver()->getRelativeDirectory();
    }

    /**
     * Get a well formatted URL with an optional action
     *
     * @param string      $path
     * @param string|null $action
     * @return string
     */
    public function to($path, $action = null)
    {
        return call_user_func_array(
            array($this->getDefaultResolver(), 'to'),
            func_get_args());
    }

    /**
     * Get the URL to a collection
     *
     * @param \Collection $page
     * @param string|null $action
     * @return string
     */
    public function page(\Collection $page, $action = null)
    {
        return call_user_func_array(
            array($this->getDefaultResolver(), 'to'),
            func_get_args());
    }

    /**
     * Get the URL to a route
     *
     * @param array $args
     * @return string
     */
    public function route(array $args)
    {
        return call_user_func_array(
            array($this->getDefaultResolver(), 'to'),
            func_get_args());
    }

}
