<?php
namespace Concrete\Core\Page\Theme;

use Concrete\Core\Utility\Service\Text;

/**
 * Class RouteCollection. Holds specific special route/theme combinations. These are functions
 * that used to live in the Router class but it makes no sense for them to take up space there.
 * @package Concrete\Core\Page\Theme
 */
class ThemeRouteCollection
{

    protected $themePaths = [];

    /**
     * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes.
     *
     * @param $path string
     * @param $theme object, if null site theme is default
     */
    public function setThemeByRoute($path, $theme = null, $wrapper = FILENAME_THEMES_VIEW)
    {
        $this->themePaths[$path] = array($theme, $wrapper);
    }

    public function setThemesByRoutes(array $routes)
    {
        foreach ($routes as $route => $theme) {
            if (is_array($theme)) {
                $this->setThemeByRoute($route, $theme[0], $theme[1]);
            } else {
                $this->setThemeByRoute($route, $theme);
            }
        }
    }

    /**
     * This grabs the theme for a particular path, if one exists in the themePaths array. Returns an array with
     * the theme handle as the first entry and the wrapper file for views as the second.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getThemeByRoute($path)
    {
        $path = (string) $path;
        if ($path === '') {
            return false;
        }
        $text = new Text();
        // there's probably a more efficient way to do this
        foreach ($this->themePaths as $lp => $layout) {
            if ($text->fnmatch($lp, $path)) {
                return $layout;
            }
        }
        return false;
    }



}