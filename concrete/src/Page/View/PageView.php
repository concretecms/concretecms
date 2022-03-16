<?php
namespace Concrete\Core\Page\View;

use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\View\Preview\PageDesignPreviewRequest;
use Concrete\Core\Page\View\Preview\PreviewRequestInterface;
use Concrete\Core\Page\View\Preview\SkinPreviewRequest;
use Concrete\Core\Page\View\Preview\ThemeCustomizerRequest;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Config;
use Environment;
use Events;
use HtmlObject\Element;
use Loader;
use PageCache;
use PageTheme;
use Permissions;
use URL;

class PageView extends View
{
    protected $c; // page
    protected $cp;
    protected $pTemplateID;
    protected $customPreviewRequest;

    public function getScopeItems()
    {
        $items = parent::getScopeItems();
        $items['c'] = $this->c;
        $items['theme'] = $this->themeObject;

        return $items;
    }

    public function getPageTemplate()
    {
        return PageTemplate::getByID($this->pTemplateID);
    }

    public function renderSinglePageByFilename($cFilename, $pkgHandle = null)
    {
        $this->loadViewThemeObject();
        $env = Environment::get();
        $cFilename = trim($cFilename, '/');

        // if we have this exact template in the theme, we use that as the outer wrapper and we don't do an inner content file
        $exactThemeTemplate = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $cFilename, $this->themePkgHandle);
        if ($exactThemeTemplate->exists()) {
            $this->setViewTemplate($exactThemeTemplate->file);
        } else {
            // use a content wrapper from themes/core if specified
            // e.g. $this->render('your/page', 'none') would use themes/core/none.php to print the $innerContent without a wrapper
            $coreThemeTemplate = $env->getRecord(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $this->themePkgHandle . '.php');
            if ($coreThemeTemplate->exists()) {
                $this->setViewTemplate($coreThemeTemplate->file);
            } else {
                // check for other themes or in a package if one was specified
                $themeTemplate = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $this->controller->getThemeViewTemplate(), $this->themePkgHandle);
                if ($themeTemplate->exists()) {
                    $this->setViewTemplate($themeTemplate->file);
                } else {
                    // fall back to the active theme wrapper if nothing else was found
                    $fallbackTheme = PageTheme::getByHandle($this->themeHandle);
                    $fallbackPkgHandle = ($fallbackTheme instanceof PageTheme) ? $fallbackTheme->getPackageHandle() : $this->themePkgHandle;
                    $fallbackTemplate = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $this->controller->getThemeViewTemplate(), $fallbackPkgHandle);
                    $path = $fallbackTemplate->file;
                    if (basename($path) != FILENAME_THEMES_VIEW) {
                        // We're going to check to see if this file actually exists in the theme. Otherwise we're going to use the default wrapper.
                        // Ideally this would happen in getThemeViewTemplate but it's hard to add the logic there.
                        if (!$fallbackTemplate->exists()) {
                            $path = $env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $fallbackPkgHandle);
                        }
                    }
                    $this->setViewTemplate($path);
                }
            }

            // set the inner content for the theme wrapper we found
            if (!isset($pkgHandle)) { // This way we can pass in a false and skip this.
                $pkgHandle = $this->c->getPackageHandle();
            }

            $this->setInnerContentFile(
                $env->getPath(
                    DIRNAME_PAGES . '/' . $cFilename,
                    $pkgHandle
                )
            );
        }
    }

    public function setupRender()
    {
        $env = Environment::get();

        if (isset($this->innerContentFile)) {
            // this has already been rendered (e.g. by calling $this->render()
            // from within a controller. So we don't reset it.
            return false;
        }

        if ($this->c->getPageTypeID() == 0 && $this->c->getCollectionFilename()) {
            $this->renderSinglePageByFilename($this->c->getCollectionFilename());
        } else {
            $this->loadViewThemeObject();
            $pt = $this->getPageTemplate();
            $rec = null;
            if ($pt) {
                $rec = $env->getRecord(
                    DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php',
                    $this->themePkgHandle);
            }
            if ($rec && $rec->exists()) {
                $this->setViewTemplate(
                    $env->getPath(
                        DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php',
                        $this->themePkgHandle));
            } else {
                $pTemplatePkgHandle = isset($this->pTemplatePkgHandle) ? $this->pTemplatePkgHandle : null;
                $rec = $env->getRecord(
                    DIRNAME_PAGE_TEMPLATES . '/' . $this->c->getPageTemplateHandle() . '.php',
                    $pTemplatePkgHandle);
                if ($rec->exists()) {
                    $this->setInnerContentFile(
                        $env->getPath(
                            DIRNAME_PAGE_TEMPLATES . '/' . $this->c->getPageTemplateHandle() . '.php',
                            $pTemplatePkgHandle));
                    $this->setViewTemplate(
                        $env->getPath(
                            DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $this->controller->getThemeViewTemplate(),
                            $this->themePkgHandle));
                } else {
                    $this->setViewTemplate(
                        $env->getPath(
                            DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_DEFAULT,
                            $this->themePkgHandle));
                }
            }
        }
    }

    public function startRender()
    {
        parent::startRender();
        $this->c->outputCustomStyleHeaderItems();
        // do we have any custom menu plugins?
        $cp = new Permissions($this->c);
        $this->cp = $cp;
        if ($cp->canViewToolbar()) {
            $dh = Loader::helper('concrete/dashboard');
            if (!$dh->inDashboard()
                && $this->c->getCollectionPath() != '/page_not_found'
                && $this->c->getCollectionPath() != '/download_file'
                && !$this->c->isPageDraft()
                && !$this->c->isMasterCollection()
            ) {
                $app = Application::getFacadeApplication();
                $u = $app->make(User::class);
                $u->markPreviousFrontendPage($this->c);
            }
        }
    }

    public function finishRender($contents)
    {
        $contents = parent::finishRender($contents);

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('contents', $contents);
        Events::dispatch('on_page_output', $event);
        $contents = $event->getArgument('contents');

        $cache = PageCache::getLibrary();
        $shouldAddToCache = $cache->shouldAddToCache($this);
        if ($shouldAddToCache) {
            $cache->set($this->c, $contents);
        }

        return $contents;
    }

    /**
     * @deprecated
     */
    public function getCollectionObject()
    {
        return $this->getPageObject();
    }

    public function getPageObject()
    {
        return $this->c;
    }

    public function section($url)
    {
        if (!empty($this->viewPath)) {
            $url = '/' . trim($url, '/');
            if (strpos($this->viewPath, $url) !== false && strpos($this->viewPath, $url) == 0) {
                return true;
            }
        }
    }

    protected function constructView($page = false)
    {
        $this->c = $page;
        parent::constructView($page->getCollectionPath());
        if (!isset($this->pTemplateID)) {
            $this->pTemplateID = $this->c->getPageTemplateID();
            $pto = $this->c->getPageTemplateObject();
            if ($pto && $pto->getPackageID()) {
                $this->pTemplatePkgHandle = $this->c->getPageTemplateObject()->getPackageHandle();
            }
        }
    }

    /**
     * @deprecated. Previewing functions should use `setCustomPreviewRequest` below – but the legacy customizer
     * cannot, because it needs to have access to a modified theme object from within this context, so we need
     * to be able to actually set it through. Don't use this method.
     *
     * Called from previewing functions, this lets us override the page's theme with one of our own choosing.
     */
    public function setCustomPageTheme(PageTheme $pt)
    {
        $this->themeObject = $pt;
        $this->themePkgHandle = $pt->getPackageHandle();
    }


    /**
     * @param mixed $customPreviewRequest
     */
    public function setCustomPreviewRequest(PreviewRequestInterface $customPreviewRequest): void
    {
        $this->customPreviewRequest = $customPreviewRequest;
        if ($customPreviewRequest instanceof PageDesignPreviewRequest) {
            if ($customPreviewRequest->getPageTemplate()) {
                $this->pTemplateID = $customPreviewRequest->getPageTemplate()->getPageTemplateID();
            }
            if ($customPreviewRequest->getTheme()) {
                $this->themeHandle = $customPreviewRequest->getTheme()->getThemeHandle();
                $this->themePkgHandle = $customPreviewRequest->getTheme()->getPackageHandle();
            }
        }
    }

    public function getThemeStyles()
    {
        $customStyles = null;
        if (isset($this->customPreviewRequest)) {
            if ($this->customPreviewRequest instanceof SkinPreviewRequest) {
                $skinIdentifier = $this->customPreviewRequest->getSkin()->getIdentifier();
                $skin = $this->themeObject->getSkinByIdentifier($skinIdentifier);
                $stylesheet = $skin->getStylesheet();
            }
            if ($this->customPreviewRequest instanceof ThemeCustomizerRequest) {
                $customStyles = $this->customPreviewRequest->getCustomCss();
            }
        } else {
            $skin = $this->c->getPageSkin();
            if (!$skin) {
                // This shouldn't happen during normal run, but in some cases like we're previewing a board
                // in the dashboard we might have a page that has no skin (because it's a dashboard page)
                // but rendering in a theme that does, so we should default to the theme's default skin in that case
                $skin = $this->themeObject->getSkinByIdentifier(SkinInterface::SKIN_DEFAULT);
            }
            $stylesheet = $skin->getStylesheet();
        }
        if ($customStyles) {
            $styles = new Element('style', $customStyles);
            $styles->type('text/css');
            return $styles;
        } else {
            return $stylesheet;
        }
    }

    /**
     * @deprecated
     * @param $stylesheet
     * @return string
     */
    public function getStyleSheet($stylesheet)
    {
        if ($this->themeObject->isThemePreviewRequest()) {
            return $this->themeObject->getStylesheet($stylesheet);
        }

        if ($this->c->hasPageThemeCustomizations()) {
            // page has theme customizations, check if we need to serve an uncached version of the style sheet,
            // either because caching is deactivated or because the version is not approved yet
            if ($this->c->getVersionObject()->isApprovedNow()) {
                // approved page, return handler script if caching is deactivated
                if (!Config::get('concrete.cache.theme_css')) {
                    return URL::to('/ccm/system/css/page', $this->c->getCollectionID(), $stylesheet);
                }
            } else {
                // this means that we're potentially viewing customizations that haven't been approved yet. So we're going to
                // pipe them all through a handler script, basically uncaching them.
                return URL::to('/ccm/system/css/page', $this->c->getCollectionID(), $stylesheet, $this->c->getVersionID());
            }
        }

        $env = Environment::get();
        $output = Config::get('concrete.cache.directory') . '/pages/' . $this->c->getCollectionID() . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle();
        $relative = REL_DIR_FILES_CACHE . '/pages/' . $this->c->getCollectionID() . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle();
        $r = $env->getRecord(
            DIRNAME_THEMES . '/' . $this->themeObject->getThemeHandle() . '/' . DIRNAME_CSS . '/' . $stylesheet,
            $this->themeObject->getPackageHandle());
        if ($r->exists()) {
            $sheetObject = new \Concrete\Core\StyleCustomizer\Stylesheet(
                $stylesheet,
                $r->file,
                $r->url,
                $output,
                $relative);
            if ($sheetObject->outputFileExists()) {
                return $sheetObject->getOutputRelativePath();
            } else {
                // cache output file doesn't exist, check if page has theme customizations
                if ($this->c->hasPageThemeCustomizations()) {
                    // build style sheet with page theme customizations
                    $style = $this->c->getCustomStyleObject();
                    if (is_object($style)) {
                        $scl = $style->getValueList();
                        $collection = app(NormalizedVariableCollectionFactory::class)->createFromStyleValueList($scl);
                        $sheetObject->setVariableCollection($collection);
                        // write cache output file
                        $sheetObject->output();
                        // return cache output file
                        return $sheetObject->getOutputRelativePath();
                    }
                }
            }

            return $this->themeObject->getStylesheet($stylesheet);
        }

        /*
         * deprecated - but this is for backward compatibility. If we don't have a stylesheet in the css/
         * directory we just pass through and return the passed file in the current directory.
         */
        return $env->getURL(
            DIRNAME_THEMES . '/' . $this->themeObject->getThemeHandle() . '/' . $stylesheet,
            $this->themeObject->getPackageHandle()
        );
    }
}
