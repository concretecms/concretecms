<?php
namespace Concrete\Core\Routing;

use Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

interface RouterInterface
{
    /**
     * Get the context that the router is running in
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

    public function register($rtPath, $callback, $rtHandle = null, $additionalAttributes = array());

    public function registerMultiple(array $routes);

    public function execute(Route $route, $parameters);

    /**
     * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
     * @access public
     * @param $path string
     * @param $theme object, if null site theme is default
     * @return void
     */
    public function setThemeByRoute($path, $theme = null, $wrapper = FILENAME_THEMES_VIEW);

    public function setThemesbyRoutes(array $routes);

    /**
     * This grabs the theme for a particular path, if one exists in the themePaths array
     * @param string $path
     * @return string|boolean
     */
    public function getThemeByRoute($path);

    public function route($data);
}
