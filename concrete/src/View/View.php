<?php
namespace Concrete\Core\View;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\Output\StandardFormatter;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Http\ResponseAssetGroup;
use Environment;
use Events;
use Concrete\Core\Support\Facade\Facade;
use PageTheme;
use Page;
use Config;
use Illuminate\Filesystem\Filesystem;

class View extends AbstractView
{
    protected $viewPath;
    protected $innerContentFile;
    protected $themeHandle;
    protected $themeObject;
    protected $themeRelativePath;
    protected $themeAbsolutePath;
    protected $viewPkgHandle;
    protected $themePkgHandle;
    protected $viewRootDirectoryName = DIRNAME_VIEWS;

    protected function constructView($path = false)
    {
        $path = '/'.trim($path, '/');
        $this->viewPath = $path;
    }

    public function setPackageHandle($pkgHandle)
    {
        $this->viewPkgHandle = $pkgHandle;
    }

    public function getThemeDirectory()
    {
        return $this->themeAbsolutePath;
    }
    public function getViewPath()
    {
        return $this->viewPath;
    }
    /**
     * gets the relative theme path for use in templates.
     *
     *
     * @return string $themePath
     */
    public function getThemePath()
    {
        return $this->themeRelativePath;
    }
    public function getThemeHandle()
    {
        return $this->themeHandle;
    }

    public function setInnerContentFile($innerContentFile)
    {
        $this->innerContentFile = $innerContentFile;
    }

    public function setViewRootDirectoryName($directory)
    {
        $this->viewRootDirectoryName = $directory;
    }

    public function inc($file, $args = [])
    {
        $__data__ = [
            'scopedItems' => $this->getScopeItems(),
        ];
        if ($args && is_array($args)) {
            $__data__['scopedItems'] += $args;
        }
        $env = Environment::get();
        $path = $env->getPath(DIRNAME_THEMES.'/'.$this->themeHandle.'/'.$file, $this->themePkgHandle);
        if (!file_exists($path)) {
            $path2 = $env->getPath(DIRNAME_THEMES.'/'.$this->themeHandle.'/'.$file, $this->viewPkgHandle);
            if (file_exists($path2)) {
                $path = $path2;
            }
            unset($path2);
        }
        $__data__['path'] = $path;
        unset($file);
        unset($args);
        unset($env);
        unset($path);
        if (!empty($__data__['scopedItems'])) {
            if (array_key_exists('__data__', $__data__['scopedItems'])) {
                throw new \Exception(t(/*i18n: %1$s is a variable name, %2$s is a function name*/'Illegal variable name \'%1$s\' in %2$s args.', '__data__', __CLASS__.'::'.__METHOD__));
            }
            extract($__data__['scopedItems']);
        }
        include $__data__['path'];
    }

    /**
     * A shortcut to posting back to the current page with a task and optional parameters. Only works in the context of.
     *
     * @param string $action
     * @param string $task
     *
     * @return string $url
     */
    public function action($action)
    {
        $a = func_get_args();
        $controllerPath = $this->controller->getControllerActionPath();
        array_unshift($a, $controllerPath);
        $ret = call_user_func_array([$this, 'url'], $a);

        return $ret;
    }

    public function setViewTheme($theme)
    {
        if (is_object($theme)) {
            $this->themeHandle = $theme->getThemeHandle();
        } else {
            $this->themeHandle = $theme;
        }

        if (isset($this->themeObject)) {
            $this->themeObject = null;
            $this->loadViewThemeObject();
        }
    }

    /**
     * Load all the theme-related variables for which theme to use for this request.
     */
    protected function loadViewThemeObject()
    {
        $env = Environment::get();
        if ($this->themeHandle) {
            switch ($this->themeHandle) {
                case VIEW_CORE_THEME:
                    $this->themeObject = new \Concrete\Theme\Concrete\PageTheme();
                    $this->pkgHandle = false;
                    break;
                case 'dashboard':
                    $this->themeObject = new \Concrete\Theme\Dashboard\PageTheme();
                    $this->pkgHandle = false;
                    break;
                default:
                    if (!isset($this->themeObject)) {
                        $this->themeObject = PageTheme::getByHandle($this->themeHandle);
                        $this->themePkgHandle = $this->themeObject->getPackageHandle();
                    }

            }
            $this->themeAbsolutePath = $env->getPath(DIRNAME_THEMES.'/'.$this->themeHandle, $this->themePkgHandle);
            $this->themeRelativePath = $env->getURL(DIRNAME_THEMES.'/'.$this->themeHandle, $this->themePkgHandle);
        }
    }

    /**
     * Begin the render.
     */
    public function start($state)
    {
    }

    public function setupRender()
    {
        // Set the theme object that we should use for this requested page.
        // Only run setup if the theme is unset. Usually it will be but if we set it
        // programmatically we already have a theme.
        $this->loadViewThemeObject();
        $env = Environment::get();
        if (!$this->innerContentFile) { // will already be set in a legacy tools file
            $this->setInnerContentFile($env->getPath($this->viewRootDirectoryName.'/'.trim($this->viewPath, '/').'.php', $this->viewPkgHandle));
        }
        if ($this->themeHandle) {
            $templateFile = FILENAME_THEMES_VIEW;
            if (is_object($this->controller)) {
                $templateFile = $this->controller->getThemeViewTemplate();
            }
            $this->setViewTemplate($env->getPath(DIRNAME_THEMES.'/'.$this->themeHandle.'/'.$templateFile, $this->themePkgHandle));
        }
    }

    public function startRender()
    {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('view', $this);
        Events::dispatch('on_start', $event);
        parent::startRender();
    }

    protected function onBeforeGetContents()
    {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('view', $this);
        Events::dispatch('on_before_render', $event);
        $this->themeObject->registerAssets();
    }

    public function renderViewContents($scopeItems)
    {
        $contents = '';

        // Render the main view file
        if ($this->innerContentFile) {
            $contents = $this->renderInnerContents($scopeItems);
        }

        // Render the template around it
        if (file_exists($this->template)) {
            $contents = $this->renderTemplate($scopeItems, $contents);
        }

        return $contents;
    }

    /**
     * Render the file set to $this->innerContentFile
     * @param $scopeItems
     * @return string
     */
    protected function renderInnerContents($scopeItems)
    {
        // Extract the items into the current scope
        extract($scopeItems);

        ob_start();
        include $this->innerContentFile;
        $innerContent = ob_get_contents();
        ob_end_clean();

        return $innerContent;
    }

    /**
     * Render the file set to $this->template
     * @param $scopeItems
     * @return string
     */
    protected function renderTemplate($scopeItems, $innerContent)
    {
        // Extract the items into the current scope
        extract($scopeItems);

        ob_start();

        // Fire a `before` event
        $this->onBeforeGetContents();
        include $this->template;

        // Fire an `after` event
        $this->onAfterGetContents();
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public function finishRender($contents)
    {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('view', $this);
        Events::dispatch('on_render_complete', $event);

        return $contents;
    }

    /**
     * Function responsible for outputting header items.
     */
    public function markHeaderAssetPosition()
    {
        echo '<!--ccm:assets:'.Asset::ASSET_POSITION_HEADER.'//-->';
    }

    /**
     * Function responsible for outputting footer items.
     */
    public function markFooterAssetPosition()
    {
        echo '<!--ccm:assets:'.Asset::ASSET_POSITION_FOOTER.'//-->';
    }

    protected function getAssetsToOutput()
    {
        $responseGroup = ResponseAssetGroup::get();
        $assets = $responseGroup->getAssetsToOutput();

        return $assets;
    }

    public function postProcessViewContents($contents)
    {
        $assets = $this->getAssetsToOutput();

        $contents = $this->replaceAssetPlaceholders($assets, $contents);

        // replace any empty placeholders
        $contents = $this->replaceEmptyAssetPlaceholders($contents);

        return $contents;
    }

    protected function postProcessAssets($assets)
    {
        $c = Page::getCurrentPage();
        if (!Config::get('concrete.cache.assets')) {
            return $assets;
        }

        if (!count($assets)) {
            return [];
        }

        // goes through all assets in this list, creating new URLs and post-processing them where possible.
        $segment = 0;
        $groupedAssets = [];
        for ($i = 0; $i < count($assets); ++$i) {
            $asset = $assets[$i];
            $nextasset = isset($assets[$i + 1]) ? $assets[$i + 1] : null;

            $groupedAssets[$segment][] = $asset;
            if (!($asset instanceof Asset) || !($nextasset instanceof Asset)) {
                ++$segment;
                continue;
            }

            if ($asset->getOutputAssetType() != $nextasset->getOutputAssetType()) {
                ++$segment;
                continue;
            }

            if (!$asset->assetSupportsCombination() || !$nextasset->assetSupportsCombination()) {
                ++$segment;
                continue;
            }
        }

        $return = [];
        // now we have a sub assets array with different segments split by whether they can be combined.

        foreach ($groupedAssets as $assets) {
            if (
                ($assets[0] instanceof Asset)
                &&
                (
                    (count($assets) > 1)
                    ||
                    $assets[0]->assetSupportsMinification()
                )
            ) {
                $class = get_class($assets[0]);
                $assets = call_user_func([$class, 'process'], $assets);
            }
            $return = array_merge($return, $assets);
        }

        return $return;
    }

    protected function replaceEmptyAssetPlaceholders($pageContent)
    {
        foreach (['<!--ccm:assets:'.Asset::ASSET_POSITION_HEADER.'//-->', '<!--ccm:assets:'.Asset::ASSET_POSITION_FOOTER.'//-->'] as $comment) {
            $pageContent = str_replace($comment, '', $pageContent);
        }

        return $pageContent;
    }

    protected function replaceAssetPlaceholders($outputAssets, $pageContent)
    {
        $outputItems = [];
        foreach ($outputAssets as $position => $assets) {
            $output = '';
            $transformed = $this->postProcessAssets($assets);
            foreach ($transformed as $item) {
                $itemstring = (string) $item;
                if (!in_array($itemstring, $outputItems)) {
                    $output .= $this->outputAssetIntoView($item);
                    $outputItems[] = $itemstring;
                }
            }
            $pageContent = str_replace('<!--ccm:assets:'.$position.'//-->', $output, $pageContent);
        }

        return $pageContent;
    }

    protected function outputAssetIntoView($item)
    {
        $formatter = new StandardFormatter();
        if ($item instanceof Asset) {
            return $formatter->output($item) . "\n";
        } else {
            return $item . "\n";
        }
    }

    public static function element($_file, $args = null, $_pkgHandle = null)
    {
        if (is_array($args)) {
            $collisions = array_intersect(['_file', '_pkgHandle'], array_keys($args));
            if ($collisions) {
                throw new \Exception(t("Illegal variable name '%s' in element args.", implode(', ', $collisions)));
            }
            $collisions = null;
            extract($args);
        }
        $view = self::getRequestInstance();

        $_c = Page::getCurrentPage();
        if (is_object($_c)) {
            $_theme = $_c->getCollectionThemeObject();
        }

        $_app = Facade::getFacadeApplication();
        $_fs = $_app->make(Filesystem::class);
        $_locator = new FileLocator($_fs, $_app);
        if (isset($_theme) && is_object($_theme)) {
            $_locator->addLocation(new FileLocator\ThemeElementLocation($_theme));
        }
        if ($_pkgHandle) {
            $_locator->addPackageLocation($_pkgHandle);
        }

        $_record = $_locator->getRecord(DIRNAME_ELEMENTS . '/' . $_file . '.php');
        $_file = $_record->getFile();

        unset($_record);
        unset($_app);
        unset($_fs);
        unset($_locator);
        unset($_theme);

        include $_file;
    }
}
