<?php
namespace Concrete\Core\View;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Http\ResponseAssetGroup;
use Environment;
use Events;
use PageTheme;
use Page;
use Loader;
use Config;

class View extends AbstractView {

    protected $viewPath;
    protected $innerContentFile;
    protected $themeHandle;
    protected $themeObject;
    protected $themeRelativePath;
    protected $themeAbsolutePath;
    protected $pkgHandle;
    protected $viewRootDirectoryName = DIRNAME_VIEWS;

    protected function constructView($path = false) {
        $path = '/' . trim($path, '/');
        $this->viewPath = $path;
    }

    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    public function getThemeDirectory() {return $this->themeAbsolutePath;}
    public function getViewPath() {return $this->viewPath;}
    /**
     * gets the relative theme path for use in templates
     * @access public
     * @return string $themePath
    */
    public function getThemePath() { return $this->themeRelativePath; }
    public function getThemeHandle() {return $this->themeHandle;}

    public function setInnerContentFile($innerContentFile) {
        $this->innerContentFile = $innerContentFile;
    }

    public function setViewRootDirectoryName($directory) {
        $this->viewRootDirectoryName = $directory;
    }

    public function inc($file, $args = array()) {
        extract($args);
        extract($this->getScopeItems());
        $env = Environment::get();
        include($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $file, $this->pkgHandle));
    }

    /**
     * A shortcut to posting back to the current page with a task and optional parameters. Only works in the context of
     * @param string $action
     * @param string $task
     * @return string $url
     */
    public function action($action) {
        $a = func_get_args();
        $controllerPath = $this->controller->getControllerActionPath();
        array_unshift($a, $controllerPath);
        $ret = call_user_func_array(array($this, 'url'), $a);
        return $ret;
    }

    public function setViewTheme($theme) {
        if (is_object($theme)) {
            $this->themeHandle = $theme->getThemeHandle();
        } else {
            $this->themeHandle = $theme;
        }
    }

    /**
     * Load all the theme-related variables for which theme to use for this request.
     */
    protected function loadViewThemeObject() {
        $env = Environment::get();
        if ($this->themeHandle) {
            if ($this->themeHandle != VIEW_CORE_THEME && $this->themeHandle != 'dashboard') {
                if (!isset($this->themeObject)) {
                    $this->themeObject = PageTheme::getByHandle($this->themeHandle);
                    $this->pkgHandle = $this->themeObject->getPackageHandle();
                }
            }
            $this->themeAbsolutePath = $env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle, $this->pkgHandle);
            $this->themeRelativePath = $env->getURL(DIRNAME_THEMES . '/' . $this->themeHandle, $this->pkgHandle);
        }
    }

    /**
     * Begin the render
     */
    public function start($state) {}

    public function setupRender() {
        // Set the theme object that we should use for this requested page.
        // Only run setup if the theme is unset. Usually it will be but if we set it
        // programmatically we already have a theme.
        $this->loadViewThemeObject();
        $env = Environment::get();
        if (!$this->innerContentFile) { // will already be set in a legacy tools file
	        $this->setInnerContentFile($env->getPath($this->viewRootDirectoryName . '/' . trim($this->viewPath, '/') . '.php', $this->pkgHandle));
        }
        if ($this->themeHandle) {
            $templateFile = FILENAME_THEMES_VIEW;
            if (is_object($this->controller)) {
                $templateFile = $this->controller->getThemeViewTemplate();
            }
            $this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $templateFile, $this->pkgHandle));
        }
    }

    public function startRender() {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('view', $this);
        Events::dispatch('on_start', $event);
        parent::startRender();
    }

    protected function onBeforeGetContents() {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('view', $this);
        Events::dispatch('on_before_render', $event);

        if ($this->themeHandle == VIEW_CORE_THEME) {
            $_pt = new \Concrete\Theme\Concrete\PageTheme();
            $_pt->registerAssets();
        } else if (is_object($this->themeObject)) {
            $this->themeObject->registerAssets();
        }
    }

    public function renderViewContents($scopeItems) {
        extract($scopeItems);
        if ($this->innerContentFile) {
            ob_start();
            include($this->innerContentFile);
            $innerContent = ob_get_contents();
            ob_end_clean();
        }

        if (file_exists($this->template)) {
            ob_start();
            $this->onBeforeGetContents();
            include($this->template);
            $contents = ob_get_contents();
            $this->onAfterGetContents();
            ob_end_clean();
            return $contents;
        } else {
            return $innerContent;
        }
    }

    public function finishRender($contents) {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('view', $this);
        Events::dispatch('on_render_complete', $event);

        return $contents;
    }

    /**
     * Function responsible for outputting header items
     * @access private
     */
    public function markHeaderAssetPosition() {
        print '<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->';
    }

    /**
     * Function responsible for outputting footer items
     * @access private
     */
    public function markFooterAssetPosition() {
        print '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->';
    }

    protected function getAssetsToOutput()
    {
        $responseGroup = ResponseAssetGroup::get();
        $assets = $responseGroup->getAssetsToOutput();
        return $assets;
    }

    public function postProcessViewContents($contents) {
        $assets = $this->getAssetsToOutput();

        $contents = $this->replaceAssetPlaceholders($assets, $contents);

        // replace any empty placeholders
        $contents = $this->replaceEmptyAssetPlaceholders($contents);

        return $contents;
    }

    protected function postProcessAssets($assets) {
        $c = Page::getCurrentPage();
        if (!Config::get('concrete.cache.assets')) {
            return $assets;
        }

        if (!count($assets)) {
            return array();
        }

        // goes through all assets in this list, creating new URLs and post-processing them where possible.
        $segment = 0;

        for ($i = 0; $i < count($assets); $i++) {

            $asset = $assets[$i];
            $nextasset = $assets[$i+1];

            $groupedAssets[$segment][] = $asset;
            if (!($asset instanceof Asset) || !($nextasset instanceof Asset)) {
                $segment++;
                continue;
            }

            if ($asset->getAssetType() != $nextasset->getAssetType()) {
                $segment++;
                continue;
            }

            if ($asset->assetSupportsMinification() != $nextasset->assetSupportsMinification()) {
                $segment++;
                continue;
            }

            if ($asset->assetSupportsCombination() != $nextasset->assetSupportsCombination()) {
                $segment++;
                continue;
            }

        }

        $return = array();
        // now we have a sub assets array with different segments split by whether they can be combined.

        foreach($groupedAssets as $segment => $assets) {
            if ($assets[0] instanceof Asset && $assets[0]->assetSupportsMinification()) {
                // this entire segment can be post processed together
                $class = get_class($assets[0]);
                $assets = call_user_func(array($class, 'minify'), $assets);
            } else if ($assets[0] instanceof Asset && $assets[0]->assetSupportsCombination()) {
                $class = get_class($assets[0]);
                $assets = call_user_func(array($class, 'combine'), $assets);
            }
            $return = array_merge($return, $assets);
        }

        return $return;
    }

    protected function replaceEmptyAssetPlaceholders($pageContent) {
        foreach(array('<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->', '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->') as $comment) {
            $pageContent = str_replace($comment, '', $pageContent);
        }
        return $pageContent;
    }

    protected function replaceAssetPlaceholders($outputAssets, $pageContent) {
        $outputItems = array();
        foreach($outputAssets as $position => $assets) {
            $output = '';
            $transformed = $this->postProcessAssets($assets);
            foreach($transformed as $item) {
                $itemstring = (string) $item;
                if (!in_array($itemstring, $outputItems)) {
                    $output .= $this->outputAssetIntoView($item);
                    $outputItems[] = $itemstring;
                }
            }
            $pageContent = str_replace('<!--ccm:assets:' . $position . '//-->', $output, $pageContent);
        }
        return $pageContent;
    }

    protected function outputAssetIntoView($item) {
        return $item . "\n";
    }

    public static function element($_file, $args = null, $_pkgHandle= null) {

        if (is_array($args)) {
            $collisions = array_intersect(array('_file', '_pkgHandle'), array_keys($args));
            if ($collisions) {
                throw new Exception(t("Illegal variable name '%s' in element args.", implode(', ', $collisions)));
            }
            $collisions = null;
            extract($args);
        }
        $view = self::getRequestInstance();

        include(Environment::get()->getPath(DIRNAME_ELEMENTS . '/' . $_file . '.php', $_pkgHandle));
    }

}
