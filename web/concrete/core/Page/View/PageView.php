<?
namespace Concrete\Core\Page\View;
use Loader;
use View;
use Environment;
use PageTemplate;
use User;
use Permissions;
use PageCache;
use PageTheme;
use URL;
use Core;

class PageView extends View {

    protected $c; // page
    protected $cp;
    protected $pTemplateID;
    protected $customStyleMap;

    public function getPageObject() {
        return $this->c;
    }

    protected function constructView($page) {
        $this->c = $page;
        parent::constructView($page->getCollectionPath());
        if (!isset($this->pTemplateID)) {
            $this->pTemplateID = $this->c->getPageTemplateID();
        }
        if (!isset($this->pThemeID)) {
            $this->pThemeID = $this->c->getPageTemplateID();
        }
    }

    public function getScopeItems() {
        $env = Environment::get();
        $pt = $this->themeObject;
        $items = parent::getScopeItems();
        $items['c'] = $this->c;

        if (is_object($pt)) {
            $css = Core::make('helper/css');
            $css->setSourceLocator(function($file) use ($env, $pt) {
                $rec = $env->getRecord(DIRNAME_THEMES . '/' . $pt->getThemeHandle() . '/' . $file, $pt->getPackageHandle());
                return $rec->file;
            });
            $css->setRelativeUrlRootLocator(function($file) use ($env, $pt) {
                $rec = $env->getRecord(DIRNAME_THEMES . '/' . $pt->getThemeHandle() . '/' . $file, $pt->getPackageHandle());
                return $rec->url;
            });
            $css->setCompiledOutputPath(DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $pt->getThemeHandle());
            $css->setCompiledRelativePath(REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $pt->getThemeHandle());

            $items['css'] = $css;
        }
        return $items;
    }

    /**
     * Called from previewing functions, this lets us override the page's template with one of our own choosing
     */
    public function setCustomPageTemplate(PageTemplate $pt) {
        $this->pTemplateID = $pt->getPageTemplateID();
    }

    /**
     * Called from previewing functions, this lets us override the page's theme with one of our own choosing
     */
    public function setCustomPageTheme(PageTheme $pt) {
        $this->themeHandle = $pt->getThemeHandle();
    }

    public function setupRender() {
        $this->loadViewThemeObject();
        $env = Environment::get();
        if ($this->c->getPageTypeID() == 0 && $this->c->getCollectionFilename()) {
            $cFilename = trim($this->c->getCollectionFilename(), '/');
            // if we have this exact template in the theme, we use that as the outer wrapper and we don't do an inner content file
            $r = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $cFilename);
            if ($r->exists()) {
                $this->setViewTemplate($r->file);
            } else {
                if (file_exists(DIR_FILES_THEMES_CORE . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php')) {
                    $this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php'));
                } else {
                    $this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
                }
                $this->setInnerContentFile($env->getPath(DIRNAME_PAGES . '/' . $cFilename, $this->c->getPackageHandle()));
            }
        } else {
            $pt = PageTemplate::getByID($this->pTemplateID);
            $rec = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle);
            if ($rec->exists()) {
                $this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle));
            } else {
                $rec = $env->getRecord(DIRNAME_PAGE_TYPES . '/' . $this->c->getPageTypeHandle() . '.php', $this->themePkgHandle);
                if ($rec->exists()) {
                    $this->setInnerContentFile($env->getPath(DIRNAME_PAGE_TYPES . '/' . $this->c->getPageTypeHandle() . '.php', $this->themePkgHandle));
                    $this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
                } else {
                    $this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_DEFAULT, $this->themePkgHandle));
                }
            }
        }
    }

    public function startRender() {
        parent::startRender();
        $this->c->outputCustomStyleHeaderItems();
        // do we have any custom menu plugins?
        $cp = new Permissions($this->c);
        $this->cp = $cp;
        if ($cp->canViewToolbar()) {
            $dh = Loader::helper('concrete/dashboard');
            if (!$dh->inDashboard() && $this->c->getCollectionPath() != '/page_not_found' && $this->c->isActive() && !$this->c->isMasterCollection()) {
                $u = new User();
                $u->markPreviousFrontendPage($this->c);
            }
            $ih = Loader::helper('concrete/ui/menu');
            $interfaceItems = $ih->getPageHeaderMenuItems();
            foreach($interfaceItems as $item) {
                $controller = $item->getController();
                $controller->outputAutoHeaderItems();
            }
        }
    }

    public function finishRender($contents) {
        parent::finishRender($contents);
        $cache = PageCache::getLibrary();
        $shouldAddToCache = $cache->shouldAddToCache($this);
        if ($shouldAddToCache) {
            $cache->outputCacheHeaders($this->c);
            $cache->set($this->c, $contents);
        }
        return $contents;
    }

    /**
     * @deprecated
     */
    public function getStyleSheet($stylesheet) {
        return $stylesheet;
    }

    /**
     * @deprecated
     */
    public function getCollectionObject() {return $this->getPageObject();}
    public function section($url) {
        if (!empty($this->viewPath)) {
            $url = '/' . trim($url, '/');
            if (strpos($this->viewPath, $url) !== false && strpos($this->viewPath, $url) == 0) {
                return true;
            }
        }
    }

}