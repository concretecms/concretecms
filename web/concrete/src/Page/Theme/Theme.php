<?php
namespace Concrete\Core\Page\Theme;

use \Concrete\Core\Http\ResponseAssetGroup;
use Config;
use Loader;
use Page;
use Environment;
use Core;
use \Concrete\Core\Page\Theme\File as PageThemeFile;
use \Concrete\Core\Package\PackageList;
use \Concrete\Core\Foundation\Object;
use PageTemplate;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use \Concrete\Core\Page\Single as SinglePage;
use \Concrete\Core\StyleCustomizer\Preset;
use \Concrete\Core\StyleCustomizer\CustomCssRecord;
use Concrete\Core\Page\Theme\GridFramework\Manager as GridFrameworkManager;

/**
 *
 * A page's theme is a pointer to a directory containing templates, CSS files and optionally PHP includes, images and JavaScript files.
 * Themes inherit down the tree when a page is added, but can also be set at the site-wide level (thereby overriding any previous choices.)
 * @package Pages and Collections
 * @subpackages Themes
 */
class Theme extends Object
{

    protected $pThemeName;
    protected $pThemeID;
    protected $pThemeDescription;
    protected $pThemeDirectory;
    protected $pThemeThumbnail;
    protected $pThemeHandle;
    protected $pThemeURL;
    protected $pThemeIsPreview = false;

    const E_THEME_INSTALLED = 1;
    const THEME_EXTENSION = ".php";
    const THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION = ".less";
    const FILENAME_TYPOGRAPHY_CSS = "typography.css";

    protected $stylesheetCachePath;
    protected $stylesheetCacheRelativePath = REL_DIR_FILES_CACHE;

    public function __construct()
    {
        $this->setStylesheetCachePath(Config::get('concrete.cache.directory'));
    }

    public static function getGlobalList()
    {
        return static::getList('pkgID > 0');
    }

    public static function getLocalList()
    {
        return static::getList('pkgID = 0');
    }

    public static function getListByPackage($pkg)
    {
        return static::getList('pkgID = ' . $pkg->getPackageID());
    }

    public static function getList($where = null)
    {
        if ($where != null) {
            $where = ' where ' . $where;
        }

        $db = Loader::db();
        $r = $db->query("select pThemeID from PageThemes" . $where);
        $themes = array();
        while ($row = $r->fetchRow()) {
            $pl = static::getByID($row['pThemeID']);
            $themes[] = $pl;
        }

        return $themes;
    }

    public static function getInstalledHandles()
    {
        $db = Loader::db();

        return $db->GetCol("select pThemeHandle from PageThemes");
    }

    public function providesAsset($assetType, $assetHandle)
    {
        $r = ResponseAssetGroup::get();
        $r->markAssetAsIncluded($assetType, $assetHandle);
    }

    public function requireAsset($assetType, $assetHandle)
    {
        $r = ResponseAssetGroup::get();
        $r->requireAsset($assetType, $assetHandle);
    }

    public static function getAvailableThemes($filterInstalled = true)
    {
        // scans the directory for available themes. For those who don't want to go through
        // the hassle of uploading

        $db = Loader::db();
        $dh = Loader::helper('file');

        $themes = $dh->getDirectoryContents(DIR_FILES_THEMES);
        if ($filterInstalled) {
            // strip out themes we've already installed
            $handles = $db->GetCol("select pThemeHandle from PageThemes");
            $themesTemp = array();
            foreach ($themes as $t) {
                if (!in_array($t, $handles)) {
                    $themesTemp[] = $t;
                }
            }
            $themes = $themesTemp;
        }

        if (count($themes) > 0) {
            $themesTemp = array();
            // get theme objects from the file system
            foreach ($themes as $t) {
                $th = static::getByFileHandle($t);
                if (!empty($th)) {
                    $themesTemp[] = $th;
                }
            }
            $themes = $themesTemp;
        }

        return $themes;

    }

    public static function getByFileHandle($handle, $dir = DIR_FILES_THEMES, $pkgHandle = '')
    {
        $dirt = $dir . '/' . $handle;
        if (is_dir($dirt)) {
            $res = static::getThemeNameAndDescription($dirt, $handle, $pkgHandle);

            $th = new static();
            $th->pThemeHandle = $handle;
            $th->pThemeDirectory = $dirt;
            $th->pThemeName = $res->pThemeName;
            $th->pThemeDescription = $res->pThemeDescription;
            if (strlen($res->pError) > 0) {
                $th->error = $res->pError;
            }
            switch ($dir) {
                case DIR_FILES_THEMES:
                    $th->pThemeURL = DIR_REL . '/' . DIRNAME_APPLICATION . '/' . DIRNAME_THEMES . '/' . $handle;
                    break;
            }

            return $th;
        }
    }

    /**
     * Checks the theme for a styles.xml file (which is how customizations happen.)
     * @return boolean
     *
     */
    public function isThemeCustomizable()
    {
        $env = Environment::get();
        $r = $env->getRecord(
            DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . DIRNAME_CSS . '/' . FILENAME_STYLE_CUSTOMIZER_STYLES,
            $this->getPackageHandle()
        );

        return $r->exists();
    }

    /**
     * Gets the style list object for this theme.
     * @return \Concrete\Core\StyleCustomizer\StyleList
     */
    public function getThemeCustomizableStyleList()
    {
        if (!isset($this->styleList)) {
            $env = Environment::get();
            $r = $env->getRecord(
                DIRNAME_THEMES . '/' . $this->getThemeHandle(
                ) . '/' . DIRNAME_CSS . '/' . FILENAME_STYLE_CUSTOMIZER_STYLES,
                $this->getPackageHandle()
            );
            $this->styleList = \Concrete\Core\StyleCustomizer\StyleList::loadFromXMLFile($r->file);
        }

        return $this->styleList;
    }

    /**
     * Gets a preset for this theme by handle
     */
    public function getThemeCustomizablePreset($handle)
    {
        $env = Environment::get();
        if ($this->isThemeCustomizable()) {
            $file = $env->getRecord(
                DIRNAME_THEMES . '/' . $this->getThemeHandle(
                ) . '/' . DIRNAME_CSS . '/' . DIRNAME_STYLE_CUSTOMIZER_PRESETS . '/' . $handle . static::THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION,
                $this->getPackageHandle()
            );
            if ($file->exists()) {
                $urlroot = $env->getURL(
                    DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . DIRNAME_CSS,
                    $this->getPackageHandle()
                );
                $preset = Preset::getFromFile($file->file, $urlroot);

                return $preset;
            }
        }
    }

    /**
     * Gets all presets available to this theme.
     */
    public function getThemeCustomizableStylePresets()
    {
        $presets = array();
        $env = Environment::get();
        if ($this->isThemeCustomizable()) {
            $directory = $env->getPath(
                DIRNAME_THEMES . '/' . $this->getThemeHandle(
                ) . '/' . DIRNAME_CSS . '/' . DIRNAME_STYLE_CUSTOMIZER_PRESETS,
                $this->getPackageHandle()
            );
            $urlroot = $env->getURL(
                DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . DIRNAME_CSS,
                $this->getPackageHandle()
            );
            $dh = Loader::helper('file');
            $files = $dh->getDirectoryContents($directory);
            foreach ($files as $f) {
                if (strrchr($f, '.') == static::THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION) {
                    $preset = Preset::getFromFile($directory . '/' . $f, $urlroot);
                    if (is_object($preset)) {
                        $presets[] = $preset;
                    }
                }
            }
        }
        usort(
            $presets,
            function ($a, $b) {
                if ($a->isDefaultPreset()) {
                    return -1;
                } else {
                    return strcasecmp($a->getPresetDisplayName('text'), $b->getPresetDisplayName('text'));
                }
            }
        );

        return $presets;
    }

    public function enablePreviewRequest()
    {
        $this->setStylesheetCacheRelativePath(REL_DIR_FILES_CACHE . '/preview');
        $this->setStylesheetCachePath(Config::get('concrete.cache.directory') . '/preview');
        $this->pThemeIsPreview = true;
    }

    public function resetThemeCustomStyles()
    {
        $db = Loader::db();
        $db->delete('PageThemeCustomStyles', array('pThemeID' => $this->getThemeID()));
        $sheets = $this->getThemeCustomizableStyleSheets();
        foreach ($sheets as $sheet) {
            $sheet->clearOutputFile();
        }

    }

    public function isThemePreviewRequest()
    {
        return $this->pThemeIsPreview;
    }

    public function getThemeCustomizableStyleSheets()
    {
        $sheets = array();
        $env = Environment::get();
        if ($this->isThemeCustomizable()) {
            $directory = $env->getPath(
                DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . DIRNAME_CSS,
                $this->getPackageHandle()
            );
            $dh = Loader::helper('file');
            $files = $dh->getDirectoryContents($directory);
            foreach ($files as $f) {
                if (strrchr($f, '.') == static::THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION) {
                    $sheets[] = $this->getStylesheetObject($f);
                }
            }
        }

        return $sheets;
    }

    public function getStylesheetObject($stylesheet)
    {
        $env = Environment::get();
        $output = $this->getStylesheetCachePath() . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle();
        $relative = $this->getStylesheetCacheRelativePath() . '/' . DIRNAME_CSS . '/' . $this->getThemeHandle();
        $r = $env->getRecord(
            DIRNAME_THEMES . '/' . $this->getThemeHandle() . '/' . DIRNAME_CSS . '/' . $stylesheet,
            $this->getPackageHandle()
        );

        $stylesheet = new \Concrete\Core\StyleCustomizer\Stylesheet($stylesheet, $r->file, $r->url, $output, $relative);

        return $stylesheet;
    }

    /**
     * Looks into the current CSS directory and returns a fully compiled stylesheet
     * when passed a LESS stylesheet. Also serves up custom value list values for the stylesheet if they exist.
     * @param  string $stylesheet The LESS stylesheet to compile
     * @return string             The path to the stylesheet.
     */
    public function getStylesheet($stylesheet)
    {
        $stylesheet = $this->getStylesheetObject($stylesheet);
        $style = $this->getThemeCustomStyleObject();
        if (is_object($style)) {
            $scl = $style->getValueList();
            $stylesheet->setValueList($scl);
        }
        if (!$this->isThemePreviewRequest()) {
            if (!$stylesheet->outputFileExists() || !Config::get('concrete.cache.theme_css')) {
                $stylesheet->output();
            }
        }
        $path = $stylesheet->getOutputRelativePath();
        if ($this->isThemePreviewRequest()) {
            $path .= '?ts=' . time();
        }

        return $path;
    }

    /**
     * Returns a Custom Style Object for the theme if one exists.
     */
    public function getThemeCustomStyleObject()
    {
        $db = Loader::db();
        $row = $db->FetchAssoc('select * from PageThemeCustomStyles where pThemeID = ?', array($this->getThemeID()));
        if (isset($row['pThemeID'])) {
            $o = new \Concrete\Core\Page\CustomStyle();
            $o->setThemeID($this->getThemeID());
            $o->setValueListID($row['scvlID']);
            $o->setPresetHandle($row['preset']);
            $o->setCustomCssRecordID($row['sccRecordID']);

            return $o;
        }
    }

    public function setCustomStyleObject(
        \Concrete\Core\StyleCustomizer\Style\ValueList $valueList,
        $selectedPreset = false,
        $customCssRecord = false
    ) {
        $db = Loader::db();
        $db->delete('PageThemeCustomStyles', array('pThemeID' => $this->getThemeID()));
        $sccRecordID = 0;
        if ($customCssRecord instanceof CustomCssRecord) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $preset = false;
        if ($selectedPreset) {
            $preset = $selectedPreset->getPresetHandle();
        }
        if ($customCssRecord instanceof CustomCssRecord) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $db->insert(
            'PageThemeCustomStyles',
            array(
                'pThemeID' => $this->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $preset,
                'scvlID' => $valueList->getValueListID()
            )
        );

        // now we reset all cached css files in this theme
        $sheets = $this->getThemeCustomizableStyleSheets();
        foreach ($sheets as $s) {
            $s->clearOutputFile();
        }

        $scc = new \Concrete\Core\Page\CustomStyle();
        $scc->setThemeID($this->getThemeID());
        $scc->setValueListID($valueList->getValueListID());
        $scc->setPresetHandle($preset);
        $scc->setCustomCssRecordID($sccRecordID);

        return $scc;
    }

    /**
     * @param string $pThemeHandle
     * @return PageTheme
     */
    public static function getByHandle($pThemeHandle)
    {
        $where = 'pThemeHandle = ?';
        $args = array($pThemeHandle);
        $pt = static::populateThemeQuery($where, $args);

        return $pt;
    }

    /**
     * @param int $ptID
     * @return PageTheme
     */
    public static function getByID($pThemeID)
    {
        $where = 'pThemeID = ?';
        $args = array($pThemeID);
        $pt = static::populateThemeQuery($where, $args);

        return $pt;
    }

    protected function populateThemeQuery($where, $args)
    {
        $db = Loader::db();
        $row = $db->GetRow(
            "select pThemeID, pThemeHandle, pThemeDescription, pkgID, pThemeName, pThemeHasCustomClass from PageThemes where {$where}",
            $args
        );
        $env = Environment::get();
        if ($row['pThemeID']) {
            $standardClass = '\\Concrete\Core\\Page\\Theme\\Theme';
            if ($row['pThemeHasCustomClass']) {
                $pkgHandle = PackageList::getHandle($row['pkgID']);
                $r = $env->getRecord(DIRNAME_THEMES . '/' . $row['pThemeHandle'] . '/' . FILENAME_THEMES_CLASS, $pkgHandle);
                $prefix = $r->override ? true : $pkgHandle;
                $customClass = core_class(
                    'Theme\\' .
                    Loader::helper('text')->camelcase($row['pThemeHandle']) .
                    '\\PageTheme',
                $prefix);
                try {
                    $pl = Core::make($customClass);
                } catch(\ReflectionException $e) {
                    $pl = Core::make($standardClass);
                }
            } else {
                $pl = Core::make($standardClass);
            }
            $pl->setPropertiesFromArray($row);
            $pkgHandle = $pl->getPackageHandle();
            $pl->pThemeDirectory = $env->getPath(DIRNAME_THEMES . '/' . $row['pThemeHandle'], $pkgHandle);
            $pl->pThemeURL = $env->getURL(DIRNAME_THEMES . '/' . $row['pThemeHandle'], $pkgHandle);

            return $pl;
        }
    }

    public static function add($pThemeHandle, $pkg = null)
    {
        if (is_object($pkg)) {
            if (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) {
                $dir = DIR_PACKAGES . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pThemeHandle;
            } else {
                $dir = DIR_PACKAGES_CORE . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_THEMES . '/' . $pThemeHandle;
            }
            $pkgID = $pkg->getPackageID();
        } else {
            if (is_dir(DIR_FILES_THEMES . '/' . $pThemeHandle)) {
                $dir = DIR_FILES_THEMES . '/' . $pThemeHandle;
                $pkgID = 0;
            } else {
                $dir = DIR_FILES_THEMES_CORE . '/' . $pThemeHandle;
                $pkgID = 0;
            }
        }
        $l = static::install($dir, $pThemeHandle, $pkgID);

        return $l;
    }

    // grabs all files in theme that are PHP based (or html if we go that route) and then
    // lists them out, by type, allowing people to install them as page type, etc...
    public function getFilesInTheme()
    {

        $dh = Loader::helper('file');
        $templateList = PageTemplate::getList();
        $cts = array();
        foreach ($templateList as $pt) {
            $pts[] = $pt->getPageTemplateHandle();
        }
        $files = array();
        $filesTmp = $dh->getDirectoryContents($this->pThemeDirectory);
        foreach ($filesTmp as $f) {
            if (strrchr($f, '.') == static::THEME_EXTENSION) {
                $fHandle = substr($f, 0, strpos($f, '.'));

                if ($f == FILENAME_THEMES_VIEW) {
                    $type = PageThemeFile::TFTYPE_VIEW;
                } elseif ($f == FILENAME_THEMES_CLASS) {
                    $type = PageThemeFile::TFTYPE_PAGE_CLASS;
                } else {
                    if ($f == FILENAME_THEMES_DEFAULT) {
                        $type = PageThemeFile::TFTYPE_DEFAULT;
                    } else {
                        if (in_array($f, SinglePage::getThemeableCorePages())) {
                            $type = PageThemeFile::TFTYPE_SINGLE_PAGE;
                        } else {
                            if (in_array($fHandle, $pts)) {
                                $type = PageThemeFile::TFTYPE_PAGE_TEMPLATE_EXISTING;
                            } else {
                                $type = PageThemeFile::TFTYPE_PAGE_TEMPLATE_NEW;
                            }
                        }
                    }
                }

                $pf = new PageThemeFile();
                $pf->setFilename($f);
                $pf->setType($type);
                $files[] = $pf;
            }
        }

        return $files;
    }

    private static function getThemeNameAndDescription($dir, $pThemeHandle, $pkgHandle = '')
    {
        $res = new \stdClass();
        $res->pThemeName = '';
        $res->pThemeDescription = '';
        $res->pError = '';
        if (file_exists($dir . '/' . FILENAME_THEMES_DESCRIPTION)) {
            $con = file($dir . '/' . FILENAME_THEMES_DESCRIPTION);
            $res->pThemeName = trim($con[0]);
            $res->pThemeDescription = trim($con[1]);
        }
        $pageThemeFile = $dir . '/' . FILENAME_THEMES_CLASS;
        if (is_file($pageThemeFile)) {
            try {
                $cn .= '\\Theme\\' . camelcase($pThemeHandle) . '\\PageTheme';
                $classNames = array();
                if (strlen($pkgHandle)) {
                    $classNames[] = '\\Concrete\\Package\\' . camelcase($pkgHandle) . $cn;
                } else {
                    $classNames[] = '\\Application' . $cn;
                    $classNames[] = '\\Concrete' . $cn;
                }
                $className = null;
                foreach ($classNames as $cn) {
                    if (class_exists($cn, false)) {
                        $className = $cn;
                        break;
                    }
                }
                if (is_null($className)) {
                    include_once $pageThemeFile;
                    foreach ($classNames as $cn) {
                        if (class_exists($cn, false)) {
                            $className = $cn;
                            break;
                        }
                    }
                }
                if (is_null($className)) {
                    $res->pError = t(/*i18n: %1$s is a filename, %2$s is a PHP class name */'The theme file %1$s does not defines the class %2$s', FILENAME_THEMES_CLASS, ltrim($classNames[0], '\\'));
                } else {
                    $instance = new $className();
                    $extensionOf = '\\Concrete\\Core\\Page\\Theme\\Theme';
                    if (!is_a($instance, $extensionOf)) {
                        $res->pError = t(/*i18n: %1$s is a filename, %2$s and %3$s are PHP class names */'The theme file %1$s should define a %2$s class that extends the class %3$s', FILENAME_THEMES_CLASS, ltrim($className, '\\'), ltrim($extensionOf, '\\'));
                    } else {
                        if (method_exists($instance, 'getThemeName')) {
                            $s = $instance->getThemeName();
                            if (strlen($s) > 0) {
                                $res->pThemeName = $s;
                            }
                        }
                        if (method_exists($instance, 'getThemeDescription')) {
                            $s = $instance->getThemeDescription();
                            if (strlen($s) > 0) {
                                $res->pThemeDescription = $s;
                            }
                        }
                    }
                }
            } catch (\Exception $x) {
                $res->pError = $x->getMessage();
            }
        }

        return $res;
    }

    public static function exportList($xml)
    {
        $nxml = $xml->addChild('themes');
        $list = static::getList();
        $pst = static::getSiteTheme();

        foreach ($list as $pt) {
            $activated = 0;
            if ($pst->getThemeID() == $pt->getThemeID()) {
                $activated = 1;
            }
            $type = $nxml->addChild('theme');
            $type->addAttribute('handle', $pt->getThemeHandle());
            $type->addAttribute('package', $pt->getPackageHandle());
            $type->addAttribute('activated', $activated);
        }

    }

    protected static function install($dir, $pThemeHandle, $pkgID)
    {
        $result = null;
        if (is_dir($dir)) {
            $pkg = null;
            if ($pkgID) {
                $pkg = \Concrete\Core\Package\Package::getByID($pkgID);
            }
            $db = Loader::db();
            $cnt = $db->getOne("select count(pThemeID) from PageThemes where pThemeHandle = ?", array($pThemeHandle));
            if ($cnt > 0) {
                throw new \Exception(static::E_THEME_INSTALLED);
            }
            $res = static::getThemeNameAndDescription($dir, $pThemeHandle, is_object($pkg) ? $pkg->getPackageHandle() : '');
            if (strlen($res->pError) === 0) {
                $pThemeName = $res->pThemeName;
                $pThemeDescription = $res->pThemeDescription;
                $db->query(
                    "insert into PageThemes (pThemeHandle, pThemeName, pThemeDescription, pkgID) values (?, ?, ?, ?)",
                    array($pThemeHandle, $pThemeName, $pThemeDescription, $pkgID)
                );

                $env = Environment::get();
                $env->clearOverrideCache();

                $pt = static::getByID($db->Insert_ID());
                $pt->updateThemeCustomClass();

                $result = $pt;
            }
        }

        return $result;
    }

    public function updateThemeCustomClass()
    {
        $env = Environment::get();
        $db = Loader::db();
        $r = $env->getRecord(
            DIRNAME_THEMES . '/' . $this->pThemeHandle . '/' . FILENAME_THEMES_CLASS,
            $this->getPackageHandle()
        );
        if ($r->exists()) {
            $db->Execute("update PageThemes set pThemeHasCustomClass = 1 where pThemeID = ?", array($this->pThemeID));
            $this->pThemeHasCustomClass = true;
        } else {
            $db->Execute("update PageThemes set pThemeHasCustomClass = 0 where pThemeID = ?", array($this->pThemeID));
            $this->pThemeHasCustomClass = false;
        }
    }

    public function getThemeID()
    {
        return $this->pThemeID;
    }

    public function getThemeName()
    {
        return $this->pThemeName;
    }

    /** Returns the display name for this theme (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     * @return string
     */
    public function getThemeDisplayName($format = 'html')
    {
        $value = $this->getThemeName();
        if (strlen($value)) {
            $value = t($value);
        } else {
            $value = t('(No Name)');
        }
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function getPackageHandle()
    {
        return \Concrete\Core\Package\PackageList::getHandle($this->pkgID);
    }

    /**
     * Returns whether a theme has a custom class.
     */
    public function hasCustomClass()
    {
        return $this->pThemeHasCustomClass;
    }

    public function getThemeHandle()
    {
        return $this->pThemeHandle;
    }

    public function getThemeDescription()
    {
        return $this->pThemeDescription;
    }

    public function getThemeDisplayDescription($format = 'html')
    {
        $value = $this->getThemeDescription();
        if (strlen($value)) {
            $value = t($value);
        } else {
            $value = t('(No Description)');
        }
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getThemeDirectory()
    {
        return $this->pThemeDirectory;
    }

    public function getThemeURL()
    {
        return $this->pThemeURL;
    }

    public function getThemeEditorCSS()
    {
        return $this->pThemeURL . '/' . static::FILENAME_TYPOGRAPHY_CSS;
    }

    public function setThemeURL($pThemeURL)
    {
        $this->pThemeURL = $pThemeURL;
    }

    public function setThemeDirectory($pThemeDirectory)
    {
        $this->pThemeDirectory = $pThemeDirectory;
    }

    public function setThemeHandle($pThemeHandle)
    {
        $this->pThemeHandle = $pThemeHandle;
    }

    public function setStylesheetCachePath($path)
    {
        $this->stylesheetCachePath = $path;
    }

    public function setStylesheetCacheRelativePath($path)
    {
        $this->stylesheetCacheRelativePath = $path;
    }

    public function getStylesheetCachePath()
    {
        return $this->stylesheetCachePath;
    }

    public function getStylesheetCacheRelativePath()
    {
        return $this->stylesheetCacheRelativePath;
    }

    public function isUninstallable()
    {
        return ($this->pThemeDirectory != DIR_FILES_THEMES_CORE . '/' . $this->getThemeHandle());
    }

    public function getThemeThumbnail()
    {
        if (file_exists($this->pThemeDirectory . '/' . FILENAME_THEMES_THUMBNAIL)) {
            $src = $this->pThemeURL . '/' . FILENAME_THEMES_THUMBNAIL;
        } else {
            $src = ASSETS_URL_THEMES_NO_THUMBNAIL;
        }
        $html = new \HtmlObject\Image();
        $img = $html->src($src)
            ->width(Config::get('concrete.icons.theme_thumbnail.width'))
            ->height(Config::get('concrete.icons.theme_thumbnail.height'))
            ->class('ccm-icon-theme');

        return $img;
    }

    public function applyToSite()
    {
        $db = Loader::db();
        $r = $db->query(
            "update CollectionVersions inner join Pages on CollectionVersions.cID = Pages.cID left join Packages on Pages.pkgID = Packages.pkgID set CollectionVersions.pThemeID = ? where cIsTemplate = 0 and (Packages.pkgHandle <> 'core' or pkgHandle is null or Pages.ptID > 0)",
            array($this->pThemeID)
        );
    }

    public static function getSiteTheme()
    {
        $c = Page::getByID(HOME_CID);

        return static::getByID($c->getCollectionThemeID());
    }

    public function uninstall()
    {
        $db = Loader::db();

        $db->query("delete from PageThemes where pThemeID = ?", array($this->pThemeID));
        $env = Environment::get();
        $env->clearOverrideCache();
    }

    /**
     * Special items meant to be extended by custom theme classes
     */

    protected $pThemeGridFrameworkHandle = false;

    public function registerAssets()
    {
    }

    public function supportsGridFramework()
    {
        return $this->pThemeGridFrameworkHandle != false;
    }

    /**
     * @return GridFramework|null
     */
    public function getThemeGridFrameworkObject()
    {
        if ($this->pThemeGridFrameworkHandle) {
            $framework = Core::make('manager/grid_framework')->driver($this->pThemeGridFrameworkHandle);
            return $framework;
        }
    }

    public function getThemeBlockClasses()
    {
        return array();
    }

    public function getThemeAreaClasses()
    {
        return array();
    }

    public function getThemeEditorClasses()
    {
        return array();
    }

    public function getThemeDefaultBlockTemplates()
    {
        return array();
    }

    public function getThemeResponsiveImageMap()
    {
        return array();
    }

    public function getThemeGatheringGridItemMargin()
    {
        return 20;
    }

    public function getThemeGatheringGridItemWidth()
    {
        return 150;
    }

    public function getThemeGatheringGridItemHeight()
    {
        return 150;
    }

}
