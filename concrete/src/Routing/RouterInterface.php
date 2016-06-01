<?php
namespace Concrete\Core\Routing;

use Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

interface RouterInterface
{
    /**
     * Get the context that the router is running in.
     *
     * @return RequestContext
     */
    public function getContext();

    /**
     * @param RequestContext $context
     */
    public function setContext(RequestContext $context);

    /**
     * @return UrlGeneratorInterface
     */
    public function getGenerator();

    /**
     * @param $generator
     */
    public function setGenerator(UrlGeneratorInterface $generator);

    public function getList();

    public function setRequest(Request $req);

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
     *
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
        $condition = null);

    public function registerMultiple(array $routes);

    public function execute(Route $route, $parameters);

    /**
     * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes.
     *
     * @param $path string
     * @param $theme object, if null site theme is default
     */
    public function setThemeByRoute($path, $theme = null, $wrapper = FILENAME_THEMES_VIEW);

    public function setThemesbyRoutes(array $routes);

    /**
     * This grabs the theme for a particular path, if one exists in the themePaths array.
     *
     * @param string $path
     *
     * @return string|bool
     */
    public function getThemeByRoute($path);

    public function route($data);
}
