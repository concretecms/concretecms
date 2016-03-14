<?php
namespace Concrete\Core\Package;

use BlockType;
use BlockTypeList;
use Concrete\Core\Antispam\Library as SystemAntispamLibrary;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Attribute\Set as AttributeSet;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Authentication\AuthenticationType as AuthenticationType;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Conversation\Editor\Editor as ConversationEditor;
use Concrete\Core\Conversation\Rating\Type as ConversationRatingType;
use Concrete\Core\Database\EntityManagerFactory;
use Concrete\Core\Database\EntityManagerFactoryInterface;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Editor\Snippet as SystemContentEditorSnippet;
use Concrete\Core\Feature\Category\Category as FeatureCategory;
use Concrete\Core\Feature\Feature;
use Concrete\Core\File\FileList;
use Concrete\Core\File\StorageLocation\Type\Type as StorageLocation;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use Concrete\Core\Gathering\Item\Template\Template as GatheringItemTemplate;
use Concrete\Core\Gathering\Item\Template\Type as GatheringItemTemplateType;
use Concrete\Core\Mail\Importer\MailImporter;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use Concrete\Core\User\Point\Action\Action as UserPointAction;
use Concrete\Core\Workflow\Progress\Category as WorkflowProgressCategory;
use Concrete\Core\Workflow\Type as WorkflowType;
use Core;
use Database;
use Environment;
use GroupSet;
use Job;
use Localization;
use ORM;
use Page;
use PageTemplate;
use PageTheme;
use PageType;
use PermissionKey;
use PermissionKeyCategory;
use SinglePage;

/**
 * A package can contains related components that customize concrete5. They can br easily
 * installed and uninstall by a user.
 *
 * @property string $pkgName Installed name of package
 * @property string $pkgHandle Installed handle of package. This should be provided by the ending package.
 * @property string $pkgDescription Installed description of package
 * @property bool $pkgIsInstalled True if package is installed
 * @property string $pkgVersion Version of package installed
 * @property string $pkgAvailableVersion
 */
class Package extends Object
{
    const E_PACKAGE_NOT_FOUND = 1;
    const E_PACKAGE_INSTALLED = 2;
    const E_PACKAGE_VERSION = 3;
    const E_PACKAGE_DOWNLOAD = 4;
    const E_PACKAGE_SAVE = 5;
    const E_PACKAGE_UNZIP = 6;
    const E_PACKAGE_INSTALL = 7;
    const E_PACKAGE_MIGRATE_BACKUP = 8;
    const E_PACKAGE_INVALID_APP_VERSION = 20;
    const E_PACKAGE_THEME_ACTIVE = 21;
    protected $DIR_PACKAGES_CORE = DIR_PACKAGES_CORE;
    protected $DIR_PACKAGES = DIR_PACKAGES;
    protected $REL_DIR_PACKAGES_CORE = REL_DIR_PACKAGES_CORE;
    protected $REL_DIR_PACKAGES = REL_DIR_PACKAGES;
    protected $backedUpFname = '';

    /**
     * The package ID.
     * Don't access this directly: use Package->getPackageID and Package->setPackageID.
     *
     * @var int|null
     *
     * @internal
     */
    public $pkgID = null;

    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected $config;
    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected $fileConfig;
    /**
     * @var \Concrete\Core\Database\DatabaseStructureManager
     */
    protected $databaseStructureManager;
    protected $appVersionRequired = '5.7.0';
    protected $pkgAllowsFullContentSwap = false;
    protected $pkgContentProvidesFileThumbnails = false;
    protected $pkgAutoloaderMapCoreExtensions = false;
    protected $pkgAutoloaderRegistries = array();
    protected $errorText = array();

    /** Returns the display name of a category of package items (localized and escaped accordingly to $format)
     * @param string $categoryHandle The category handle
     * @param string $format         = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public static function getPackageItemsCategoryDisplayName($categoryHandle, $format = 'html')
    {
        switch ($categoryHandle) {
            case 'attribute_categories':
                $value = t('Attribute categories');
                break;
            case 'permission_categories':
                $value = t('Permission categories');
                break;
            case 'permission_access_entity_types':
                $value = t('Permission access entity types');
                break;
            case 'attribute_keys':
                $value = t('Attribute keys');
                break;
            case 'attribute_sets':
                $value = t('Attribute sets');
                break;
            case 'group_sets':
                $value = t('Group sets');
                break;
            case 'page_types':
                $value = t('Page types');
                break;
            case 'mail_importers':
                $value = t('Mail importers');
                break;
            case 'block_types':
                $value = t('Block types');
                break;
            case 'page_themes':
                $value = t('Page themes');
                break;
            case 'permissions':
                $value = t('Permissions');
                break;
            case 'single_pages':
                $value = t('Single pages');
                break;
            case 'attribute_types':
                $value = t('Attribute types');
                break;
            case 'captcha_libraries':
                $value = t('Captcha libraries');
                break;
            case 'antispam_libraries':
                $value = t('Antispam libraries');
                break;
            case 'jobs':
                $value = t('Jobs');
                break;
            case 'workflow_types':
                $value = t('Workflow types');
                break;
            case 'workflow_progress_categories':
                $value = t('Workflow progress categories');
                break;
            case 'storage_locations':
                $value = t('Storage Locations');
                break;
            default:
                $value = t(Core::make('helper/text')->unhandle($categoryHandle));
                break;
        }
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Returns the name of an object belonging to a package.
     *
     * @param mixed $item
     *
     * @return string
     */
    public static function getItemName($item)
    {
        $txt = Core::make('helper/text');
        if ($item instanceof BlockType) {
            return t($item->getBlockTypeName());
        } elseif ($item instanceof PageTheme) {
            return $item->getThemeDisplayName();
        } elseif ($item instanceof Feature) {
            return $item->getFeatureName();
        } elseif ($item instanceof FeatureCategory) {
            return $item->getFeatureCategoryName();
        } elseif ($item instanceof GatheringDataSource) {
            return $item->getGatheringDataSourceName();
        } elseif ($item instanceof GatheringItemTemplateType) {
            return $txt->unhandle($item->getGatheringItemTemplateTypeHandle());
        } elseif ($item instanceof GatheringItemTemplate) {
            return $item->getGatheringItemTemplateName();
        } elseif ($item instanceof BlockTypeSet) {
            return $item->getBlockTypeSetDisplayName();
        } elseif ($item instanceof PageTypeComposerControlType) {
            return $item->getPageTypeComposerControlTypeDisplayName();
        } elseif ($item instanceof PageTypePublishTargetType) {
            return $item->getPageTypePublishTargetTypeDisplayName();
        } elseif ($item instanceof PageType) {
            return $item->getPageTypeName();
        } elseif ($item instanceof PageTemplate) {
            return $item->getPageTemplateDisplayName();
        } elseif ($item instanceof MailImporter) {
            return $item->getMailImporterName();
        } elseif ($item instanceof Page) {
            return $item->getCollectionPath();
        } elseif ($item instanceof AttributeType) {
            return $item->getAttributeTypeDisplayName();
        } elseif ($item instanceof PermissionAccessEntityType) {
            return $item->getAccessEntityTypeDisplayName();
        } elseif ($item instanceof PermissionKeyCategory) {
            return $txt->unhandle($item->getPermissionKeyCategoryHandle());
        } elseif ($item instanceof WorkflowProgressCategory) {
            return $txt->unhandle($item->getWorkflowProgressCategoryHandle());
        } elseif ($item instanceof AttributeKeyCategory) {
            return $txt->unhandle($item->getAttributeKeyCategoryHandle());
        } elseif ($item instanceof AttributeSet) {
            $at = AttributeKeyCategory::getByID($item->getAttributeSetKeyCategoryID());

            return t(
                '%s (%s)',
                $item->getAttributeSetDisplayName(),
                $txt->unhandle($at->getAttributeKeyCategoryHandle()));
        } elseif ($item instanceof GroupSet) {
            return $item->getGroupSetDisplayName();
        } elseif ($item instanceof AttributeKey) {
            $akc = AttributeKeyCategory::getByID($item->getAttributeKeyCategoryID());

            return t(
                ' %s (%s)',
                $txt->unhandle($item->getAttributeKeyHandle()),
                $txt->unhandle($akc->getAttributeKeyCategoryHandle()));
        } elseif ($item instanceof UserPointAction) {
            return $item->getUserPointActionName();
        } elseif ($item instanceof SystemCaptchaLibrary) {
            return $item->getSystemCaptchaLibraryName();
        } elseif ($item instanceof SystemAntispamLibrary) {
            return $item->getSystemAntispamLibraryName();
        } elseif ($item instanceof ConversationRatingType) {
            return $item->getConversationRatingTypeDisplayName();
        } elseif ($item instanceof SystemContentEditorSnippet) {
            return $item->getSystemContentEditorSnippetName();
        } elseif ($item instanceof AuthenticationType) {
            return $item->getAuthenticationTypeName();
        } elseif (is_a($item, 'PermissionKey')) {
            return $item->getPermissionKeyDisplayName();
        } elseif (is_a($item, 'Job')) {
            return $item->getJobName();
        } elseif (is_a($item, 'WorkflowType')) {
            return $item->getWorkflowTypeName();
        } elseif ($item instanceof StorageLocation) {
            return $item->getName();
        }
    }

    /**
     * This is the pre-test routine that packages run through before they are installed. Any errors that come here are
     * to be returned in the form of an array so we can show the user. If it's all good we return true.
     *
     * @param string $package Package handle
     * @param bool $testForAlreadyInstalled
     *
     * @return array|bool Returns an array of errors or true if the package can be installed
     */
    public static function testForInstall($package, $testForAlreadyInstalled = true)
    {
        // this is the pre-test routine that packages run through before they are installed. Any errors that come here
        // are to be returned in the form of an array so we can show the user. If it's all good we return true
        $db = Database::connection();
        $errors = array();

        $pkg = static::getClass($package);

        // Step 1 does that package exist ?
        if ((!is_dir(DIR_PACKAGES . '/' . $package) && (!is_dir(
                    DIR_PACKAGES_CORE . '/' . $package))) || $package == ''
        ) {
            $errors[] = self::E_PACKAGE_NOT_FOUND;
        } elseif ($pkg instanceof BrokenPackage) {
            $errors[] = self::E_PACKAGE_NOT_FOUND;
        }

        // Step 2 - check to see if the user has already installed a package w/this handle
        if ($testForAlreadyInstalled) {
            $cnt = $db->fetchColumn("SELECT count(*) FROM Packages WHERE pkgHandle = ?", array($package));
            if ($cnt > 0) {
                $errors[] = self::E_PACKAGE_INSTALLED;
            }
        }

        if (count($errors) == 0) {
            // test minimum application version requirement
            if (version_compare(APP_VERSION, $pkg->getApplicationVersionRequired(), '<')) {
                $errors[] = array(self::E_PACKAGE_VERSION, $pkg->getApplicationVersionRequired());
            }
        }

        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }

    /**
     * Returns a package's class.
     *
     * @param string $pkgHandle Handle of package
     *
     * @return Package
     */
    public static function getClass($pkgHandle)
    {
        $cache = Core::make('cache/request');
        $item = $cache->getItem('package/class/' . $pkgHandle);
        $cl = $item->get();
        if ($item->isMiss()) {
            $item->lock();
            // loads and instantiates the object

            $cl = \Concrete\Core\Foundation\ClassLoader::getInstance();
            $cl->registerPackageController($pkgHandle);

            $class = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Controller';
            try {
                $cl = Core::make($class);
            } catch (\Exception $ex) {
                $cl = new BrokenPackage($pkgHandle);
            }
            $item->set($cl);
        }

        return clone $cl;
    }

    /**
     * Returns the version of concrete5 required by the package.
     *
     * @return string
     */
    public function getApplicationVersionRequired()
    {
        return $this->appVersionRequired;
    }

    /**
     * Returns a Package object for the given package handle, null if not found.
     *
     * @param string $pkgHandle
     *
     * @return Package
     */
    public static function getByHandle($pkgHandle)
    {
        $db = Database::connection();
        $row = $db->fetchAssoc("SELECT * FROM Packages WHERE pkgHandle = ?", array($pkgHandle));
        if ($row) {
            $pkg = static::getClass($row['pkgHandle']);
            if ($pkg instanceof self) {
                $pkg->setPropertiesFromArray($row);
            }

            return $pkg;
        } else {
            return null;
        }
    }

    /**
     * Returns an array of packages that have newer versions in the local packages directory
     * than those which are in the Packages table. This means they're ready to be upgraded.
     *
     * @return Package[]
     */
    public static function getLocalUpgradeablePackages()
    {
        $packages = self::getAvailablePackages(false);
        $upgradeables = array();
        $db = Database::connection();
        foreach ($packages as $p) {
            $row = $db->fetchAssoc(
                "SELECT pkgID, pkgVersion FROM Packages WHERE pkgHandle = ? AND pkgIsInstalled = 1",
                array($p->getPackageHandle())
            );
            if ($row) {
                if (version_compare($p->getPackageVersion(), $row['pkgVersion'], '>')) {
                    $p->setPackageID($row['pkgID']);
                    $p->pkgCurrentVersion = $row['pkgVersion'];
                    $upgradeables[] = $p;
                }
            }
        }

        return $upgradeables;
    }

    /**
     * Returns all available packages.
     *
     * @param bool $filterInstalled True to only return installed packages
     *
     * @return Package[]
     */
    public static function getAvailablePackages($filterInstalled = true)
    {
        $dh = Core::make('helper/file');

        $packages = $dh->getDirectoryContents(DIR_PACKAGES);
        if ($filterInstalled) {
            $handles = self::getInstalledHandles();

            // strip out packages we've already installed
            $packagesTemp = array();
            foreach ($packages as $p) {
                if (!in_array($p, $handles)) {
                    $packagesTemp[] = $p;
                }
            }
            $packages = $packagesTemp;
        }

        if (count($packages) > 0) {
            $packagesTemp = array();
            // get package objects from the file system
            foreach ($packages as $p) {
                if (file_exists(DIR_PACKAGES . '/' . $p . '/' . FILENAME_CONTROLLER)) {
                    $pkg = static::getClass($p);
                    if (!empty($pkg)) {
                        $packagesTemp[] = $pkg;
                    }
                }
            }
            $packages = $packagesTemp;
        }

        return $packages;
    }

    /**
     * Returns all installed package handles.
     *
     * @return string[]
     */
    public static function getInstalledHandles()
    {
        $db = Database::connection();

        return $db->GetCol("SELECT pkgHandle FROM Packages");
    }

    /**
     * Finds all packages that have an upgraded version available in the marketplace.
     *
     * @return Package[]
     */
    public static function getRemotelyUpgradeablePackages()
    {
        $packages = self::getInstalledList();
        $upgradeables = array();
        foreach ($packages as $p) {
            if (version_compare($p->getPackageVersion(), $p->getPackageVersionUpdateAvailable(), '<')) {
                $upgradeables[] = $p;
            }
        }

        return $upgradeables;
    }

    /**
     * Returns an array of all installed packages.
     *
     * @return Package[]
     */
    public static function getInstalledList()
    {
        $db = Database::connection();
        $r = $db->query("SELECT * FROM Packages WHERE pkgIsInstalled = 1 ORDER BY pkgDateInstalled ASC");
        $pkgArray = array();
        while ($row = $r->fetchRow()) {
            $pkg = new self();
            $pkg->setPropertiesFromArray($row);

            $pkgArray[] = $pkg;
        }

        return $pkgArray;
    }

    /**
     * Returns the path to the package's folder, relative to the install path.
     *
     * @return string
     */
    public function getRelativePath()
    {
        $dirp = (is_dir(
            $this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->REL_DIR_PACKAGES : $this->REL_DIR_PACKAGES_CORE;

        return $dirp . '/' . $this->pkgHandle;
    }

    /**
     * Returns the package handle.
     *
     * @return string
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * Gets the date the package was added to the system.
     *
     * @return string date formatted like: 2009-01-01 00:00:00
     */
    public function getPackageDateInstalled()
    {
        return $this->pkgDateInstalled;
    }

    public function getPackageVersionUpdateAvailable()
    {
        return $this->pkgAvailableVersion;
    }

    /**
     * Returns true if the package is installed, false if not.
     *
     * @return bool
     */
    public function isPackageInstalled()
    {
        return $this->pkgIsInstalled;
    }

    /**
     * Gets the contents of the package's CHANGELOG file. If no changelog is available an empty string is returned.
     *
     * @return string
     */
    public function getChangelogContents()
    {
        if (file_exists($this->getPackagePath() . '/CHANGELOG')) {
            $contents = Core::make('helper/file')->getContents($this->getPackagePath() . '/CHANGELOG');

            return nl2br(Core::make('helper/text')->entities($contents));
        }

        return '';
    }

    public function getPackagePath()
    {
        $dirp = (is_dir(
            $this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->DIR_PACKAGES : $this->DIR_PACKAGES_CORE;
        $path = $dirp . '/' . $this->getPackageHandle();

        return $path;
    }

    /**
     * Returns the currently installed package version.
     * NOTE: This function only returns a value if getLocalUpgradeablePackages() has been called first!
     *
     * @return string
     */
    public function getPackageCurrentlyInstalledVersion()
    {
        return $this->pkgCurrentVersion;
    }

    /**
     * @return bool
     */
    public function providesCoreExtensionAutoloaderMapping()
    {
        return $this->pkgAutoloaderMapCoreExtensions;
    }

    /**
     * Returns custom autoloader prefixes registered by the class loader.
     *
     * @return array Keys represent the namespace, not relative to the package's namespace. Values are the path, and are relative to the package directory.
     */
    public function getPackageAutoloaderRegistries()
    {
        return $this->pkgAutoloaderRegistries;
    }

    /**
     * Returns true if the package has a post install screen.
     *
     * @return bool
     */
    public function hasInstallPostScreen()
    {
        return file_exists(
            $this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install_post.php');
    }

    /**
     * Returns true if the package has an install options screen.
     *
     * @return bool
     */
    public function showInstallOptionsScreen()
    {
        return $this->hasInstallNotes() || $this->allowsFullContentSwap();
    }

    public function hasInstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install.php');
    }

    public function hasUninstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/uninstall.php');
    }

    public function allowsFullContentSwap()
    {
        return $this->pkgAllowsFullContentSwap;
    }

    /**
     * Loads package translation files into zend translate.
     *
     * @param string                                  $locale    = null The identifier of the locale to activate (used to build the language file path). If empty we'll use currently active locale.
     * @param \Zend\I18n\Translator\Translator|string $translate = 'current' The Zend Translator instance that holds the translations (set to 'current' to use the current one)
     */
    public function setupPackageLocalization($locale = null, $translate = 'current')
    {
        if ($translate === 'current') {
            $translate = Localization::getTranslate();
        }
        if (is_object($translate)) {
            $path = $this->getPackagePath() . '/' . DIRNAME_LANGUAGES;
            if (!isset($locale) || !strlen($locale)) {
                $locale = Localization::activeLocale();
            }
            $languageFile = "$path/$locale/LC_MESSAGES/messages.mo";
            if (is_file($languageFile)) {
                $translate->addTranslationFile('gettext', $languageFile);
            }
        }
    }

    /**
     * @return bool|int[] true on success, array of error codes on failure
     */
    public function testForUninstall()
    {
        $errors = array();
        $items = $this->getPackageItems();
        /** @var PageTheme[] $themes */
        $themes = array_get($items, 'page_themes', array());

        // Step 1, check for active themes
        $active_theme = \PageTheme::getSiteTheme();
        foreach ($themes as $theme) {
            if ($active_theme->getThemeID() == $theme->getThemeID()) {
                $errors[] = self::E_PACKAGE_THEME_ACTIVE;
                break;
            }
        }

        return count($errors) ? $errors : true;
    }

    /**
     * Uninstalls the package. Removes any blocks, themes, or pages associated with the package.
     */
    public function uninstall()
    {
        $db = Database::connection();

        $items = $this->getPackageItems();

        foreach ($items as $k => $array) {
            if (!is_array($array)) {
                continue;
            }

            foreach ($array as $item) {
                if ($item instanceof AuthenticationType) {
                    $item->delete();
                }

                if (is_a($item, 'Job')) {
                    $item->uninstall();
                } elseif (is_a($item, 'AttributeKey') || is_a($item, 'MailImporter')) {
                    $item->delete();
                } else {
                    switch (get_class($item)) {
                        case 'BlockType':
                        case 'GatheringDataSource':
                        case 'BlockTypeSet':
                        case 'Feature':
                        case 'FeatureCategory':
                        case 'GatheringItemTemplate':
                        case 'ConversationEditor':
                        case 'ConversationRatingType':
                        case 'PageTypePublishTargetType':
                        case 'PageTypeComposerControlType':
                        case 'PageTemplate':
                            $item->delete();
                            break;
                        case 'PageTheme':
                            $item->uninstall();
                            break;
                        case 'Page':
                            @$item->delete(); // we suppress errors because sometimes the wrapper pages can delete first.
                            break;
                        case 'SystemCaptchaLibrary':
                        case 'SystemContentEditorSnippet':
                        case 'SystemAntispamLibrary':
                            $item->delete();
                            break;
                        case 'PageType':
                            $item->delete();
                            break;
                        case 'MailImporter':
                            $item->delete();
                            break;
                        case 'UserPointAction':
                        case 'AttributeKeyCategory':
                        case 'PermissionKeyCategory':
                        case 'AttributeSet':
                        case 'GroupSet':
                        case 'AttributeType':
                        case 'WorkflowType':
                        case 'WorkflowProgressCategory':
                        case 'PermissionKey':
                        case 'PermissionAccessEntityType':
                            $item->delete();
                            break;
                        default:
                            if (method_exists($item, 'delete')) {
                                $item->delete();
                            } elseif (method_exists($item, 'uninstall')) {
                                $item->uninstall();
                            }
                            break;
                    }
                }
            }
        }
        \Config::clearNamespace($this->getPackageHandle());
        \Core::make('config/database')->clearNamespace($this->getPackageHandle());

        $this->destroyProxyClasses();

        $db->executeQuery("DELETE FROM Packages WHERE pkgID = ?", array($this->pkgID));
        Localization::clearCache();
    }

    /**
     * Returns an array of package items (e.g. blocks, themes).
     *
     * @return array
     */
    public function getPackageItems()
    {
        $items = array();

        $items['attribute_categories'] = AttributeKeyCategory::getListByPackage($this);
        $items['permission_categories'] = PermissionKeyCategory::getListByPackage($this);
        $items['permission_access_entity_types'] = PermissionAccessEntityType::getListByPackage($this);
        $items['attribute_keys'] = AttributeKey::getListByPackage($this);
        $items['attribute_sets'] = AttributeSet::getListByPackage($this);
        $items['group_sets'] = GroupSet::getListByPackage($this);
        $items['page_types'] = PageType::getListByPackage($this);
        $items['page_templates'] = PageTemplate::getListByPackage($this);
        $items['mail_importers'] = MailImporter::getListByPackage($this);
        $items['gathering_item_template_types'] = GatheringItemTemplateType::getListByPackage($this);
        $items['gathering_item_templates'] = GatheringItemTemplate::getListByPackage($this);
        $items['gathering_data_sources'] = GatheringDataSource::getListByPackage($this);
        $items['features'] = Feature::getListByPackage($this);
        $items['feature_categories'] = FeatureCategory::getListByPackage($this);
        $btl = new BlockTypeList();
        $btl->filterByPackage($this);
        $blocktypes = $btl->get();
        $items['block_types'] = $blocktypes;
        $items['block_type_sets'] = BlockTypeSet::getListByPackage($this);
        $items['page_themes'] = PageTheme::getListByPackage($this);
        $items['permissions'] = PermissionKey::getListByPackage($this);
        $items['single_pages'] = SinglePage::getListByPackage($this);
        $items['attribute_types'] = AttributeType::getListByPackage($this);
        $items['captcha_libraries'] = SystemCaptchaLibrary::getListByPackage($this);
        $items['content_editor_snippets'] = SystemContentEditorSnippet::getListByPackage($this);
        $items['conversation_editors'] = ConversationEditor::getListByPackage($this);
        $items['conversation_rating_types'] = ConversationRatingType::getListByPackage($this);
        $items['page_type_publish_target_types'] = PageTypePublishTargetType::getListByPackage($this);
        $items['page_type_composer_control_types'] = PageTypeComposerControlType::getListByPackage($this);
        $items['antispam_libraries'] = SystemAntispamLibrary::getListByPackage($this);
        $items['community_point_actions'] = UserPointAction::getListByPackage($this);
        $items['jobs'] = Job::getListByPackage($this);
        $items['workflow_types'] = WorkflowType::getListByPackage($this);
        $items['workflow_progress_categories'] = WorkflowProgressCategory::getListByPackage($this);
        $items['authentication_types'] = AuthenticationType::getListByPackage($this);
        $items['storage_locations'] = StorageLocation::getListByPackage($this);
        ksort($items);

        return $items;
    }

    /**
     * Destroys all the existing proxy classes for this package.
     *
     * @return bool
     */
    protected function destroyProxyClasses()
    {
        $dbm = $this->getDatabaseStructureManager();
        $config = $dbm->getEntityManager()->getConfiguration();
        if (is_object($cache = $config->getMetadataCacheImpl())) {
            $cache->flushAll();
        }

        return $dbm->destroyProxyClasses('ConcretePackage' . camelcase($this->getPackageHandle()) . 'Src');
    }

    /**
     * Gets a package specific entity manager.
     *
     * @return \Concrete\Core\Database\DatabaseStructureManager
     */
    public function getDatabaseStructureManager()
    {
        if (!isset($this->databaseStructureManager)) {
            $this->databaseStructureManager = Core::make('database/structure', array($this->getEntityManager()));
        }

        return $this->databaseStructureManager;
    }

    /**
     * @return EntityManagerFactoryInterface
     */
    public function getEntityManagerFactory()
    {
        return new EntityManagerFactory($this->getPackageEntitiesPath());
    }

    /**
     * Gets a package specific entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return ORM::entityManager($this);
    }

    /**
     * Removes any existing pages, files, stacks, block and page types and installs content from the package.
     *
     * @param $options
     */
    public function swapContent($options)
    {
        if ($this->validateClearSiteContents($options)) {
            \Core::make('cache/request')->disable();

            $pl = new PageList();
            $pages = $pl->getResults();
            foreach ($pages as $c) {
                $c->delete();
            }

            $fl = new FileList();
            $files = $fl->getResults();
            foreach ($files as $f) {
                $f->delete();
            }

            // clear stacks
            $sl = new StackList();
            foreach ($sl->get() as $c) {
                $c->delete();
            }

            $home = Page::getByID(HOME_CID);
            $blocks = $home->getBlocks();
            foreach ($blocks as $b) {
                $b->deleteBlock();
            }

            $pageTypes = PageType::getList();
            foreach ($pageTypes as $ct) {
                $ct->delete();
            }

            // now we add in any files that this package has
            if (is_dir($this->getPackagePath() . '/content_files')) {
                $ch = new ContentImporter();
                $computeThumbnails = true;
                if ($this->contentProvidesFileThumbnails()) {
                    $computeThumbnails = false;
                }
                $ch->importFiles($this->getPackagePath() . '/content_files', $computeThumbnails);
            }

            // now we parse the content.xml if it exists.

            $ci = new ContentImporter();
            $ci->importContentFile($this->getPackagePath() . '/content.xml');

            \Core::make('cache/request')->enable();
        }
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    protected function validateClearSiteContents($options)
    {
        if (Core::make('app')->isRunThroughCommandLineInterface()) {
            $result = true;
        } else {
            $result = false;
            $u = new \User();
            if ($u->isSuperUser()) {
                // this can ONLY be used through the post. We will use the token to ensure that
                $valt = Core::make('helper/validation/token');
                if ($valt->validate('install_options_selected', $options['ccm_token'])) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Returns a path to where the packages files are located.
     *
     * @return string $path
     */
    public function contentProvidesFileThumbnails()
    {
        return $this->pkgContentProvidesFileThumbnails;
    }

    /**
     * Converts package install test errors to human-readable strings.
     *
     * @param $testResults Package install test errors
     *
     * @return array
     */
    public static function mapError($testResults)
    {
        $errorText = array(
            self::E_PACKAGE_INSTALLED => t("You've already installed that package."),
            self::E_PACKAGE_NOT_FOUND => t("Invalid Package."),
            self::E_PACKAGE_VERSION => t("This package requires concrete5 version %s or greater."),
            self::E_PACKAGE_DOWNLOAD => t("An error occurred while downloading the package."),
            self::E_PACKAGE_SAVE => t("concrete5 was not able to save the package after download."),
            self::E_PACKAGE_UNZIP => t('An error occurred while trying to unzip the package.'),
            self::E_PACKAGE_INSTALL => t('An error occurred while trying to install the package.'),
            self::E_PACKAGE_MIGRATE_BACKUP => t(
                'Unable to backup old package directory to %s',
                \Config::get('concrete.misc.package_backup_directory')
            ),
            self::E_PACKAGE_INVALID_APP_VERSION => t(
                'This package isn\'t currently available for this version of concrete5. Please contact the maintainer of this package for assistance.'
            ),
            self::E_PACKAGE_THEME_ACTIVE => t('This package contains the active site theme, please change the theme before uninstalling.'),
        );

        $testResultsText = array();
        foreach ($testResults as $result) {
            if (is_array($result)) {
                $et = $errorText[$result[0]];
                array_shift($result);
                $testResultsText[] = vsprintf($et, $result);
            } elseif (is_int($result)) {
                $testResultsText[] = $errorText[$result];
            } elseif (!empty($result)) {
                $testResultsText[] = $result;
            }
        }

        return $testResultsText;
    }

    /**
     * Returns the directory containing package entities.
     *
     * @return string
     */
    public function getPackageEntitiesPath()
    {
        return $this->getPackagePath() . '/' . DIRNAME_CLASSES;
    }

    /**
     * Called to enable package specific configuration.
     */
    public function registerConfigNamespace()
    {
        \Config::package($this->getPackageHandle(), $this->getPackagePath() . '/config');
    }

    /**
     * Get the standard database config liaison.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getConfig()
    {
        return $this->getDatabaseConfig();
    }

    /**
     * Get the standard database config liaison.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getDatabaseConfig()
    {
        if (!$this->config) {
            $this->config = new Liaison(\Core::make('config/database'), $this->getPackageHandle());
        }

        return $this->config;
    }

    /**
     * Get the standard filesystem config liaison.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getFileConfig()
    {
        if (!$this->fileConfig) {
            $this->fileConfig = new Liaison(\Core::make('config'), $this->getPackageHandle());
        }

        return $this->fileConfig;
    }

    /**
     * Installs the package info row and installs the database. Packages installing additional content should override this method, call the parent method,
     * and use the resulting package object for further installs.
     *
     * @return Package
     */
    public function install()
    {
        PackageList::refreshCache();
        $db = Database::connection();
        $dh = Core::make('helper/date');
        $v = array(
            $this->getPackageName(),
            $this->getPackageDescription(),
            $this->getPackageVersion(),
            $this->getPackageHandle(),
            1,
            $dh->getOverridableNow(),
        );
        $db->query(
            "INSERT INTO Packages (pkgName, pkgDescription, pkgVersion, pkgHandle, pkgIsInstalled, pkgDateInstalled) VALUES (?, ?, ?, ?, ?, ?)",
            $v
        );

        $pkg = self::getByID($db->lastInsertId());
        ClassLoader::getInstance()->registerPackage($pkg);
        $pkg->installDatabase();

        $env = Environment::get();
        $env->clearOverrideCache();
        Localization::clearCache();

        return $pkg;
    }

    /**
     * Returns the translated name of the package.
     *
     * @return string
     */
    public function getPackageName()
    {
        return t($this->pkgName);
    }

    /**
     * Returns the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return t($this->pkgDescription);
    }

    /**
     * Returns the installed package version.
     *
     * @return string
     */
    public function getPackageVersion()
    {
        return $this->pkgVersion;
    }

    /**
     * Returns a Package object for the given package id, null if not found.
     *
     * @param int $pkgID
     *
     * @return Package
     */
    public static function getByID($pkgID)
    {
        $db = Database::connection();
        $row = $db->fetchAssoc("SELECT * FROM Packages WHERE pkgID = ?", array($pkgID));
        if ($row) {
            $pkg = static::getClass($row['pkgHandle']);
            if ($pkg instanceof self) {
                $pkg->setPropertiesFromArray($row);

                return $pkg;
            }
        }

        return null;
    }

    /**
     * Installs the packages database through doctrine entities and db.xml
     * database definitions.
     */
    public function installDatabase()
    {
        $this->installEntitiesDatabase();

        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
    }

    public function installEntitiesDatabase()
    {
        $dbm = $this->getDatabaseStructureManager();

        if ($dbm->hasEntities()) {
            $dbm->generateProxyClasses();
            $dbm->installDatabase();
        }
    }

    /**
     * Installs a package's database from an XML file.
     *
     * @param string $xmlFile Path to the database XML file
     *
     * @return bool|\stdClass Returns false if the XML file could not be found
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public static function installDB($xmlFile)
    {
        if (!file_exists($xmlFile)) {
            return false;
        }

        $db = Database::connection();
        $db->beginTransaction();

        $parser = Schema::getSchemaParser(simplexml_load_file($xmlFile));
        $parser->setIgnoreExistingTables(false);
        $toSchema = $parser->parse($db);

        $fromSchema = $db->getSchemaManager()->createSchema();
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());

        foreach ($saveQueries as $query) {
            $db->query($query);
        }

        $db->commit();

        $result = new \stdClass();
        $result->result = false;

        return $result;
    }

    /**
     * Updates the available package number in the database.
     *
     * @param string $vNum New version number
     */
    public function updateAvailableVersionNumber($vNum)
    {
        $db = Database::connection();
        $v = array($vNum, $this->getPackageID());
        $db->query("update Packages set pkgAvailableVersion = ? where pkgID = ?", $v);
    }

    /**
     * Returns the package ID.
     *
     * @return int|null
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Sets the package ID.
     *
     * @param int|null $value
     */
    public function setPackageID($value)
    {
        $this->pkgID = empty($value) ? null : (int) $value;
    }

    /**
     * Updates a package's name, description, version and ID using the current class properties.
     */
    public function upgradeCoreData()
    {
        $db = Database::connection();
        $p1 = static::getClass($this->getPackageHandle());
        if ($p1 instanceof self) {
            $v = array(
                $p1->getPackageName(),
                $p1->getPackageDescription(),
                $p1->getPackageVersion(),
                $this->getPackageID(),
            );
            $db->query("update Packages set pkgName = ?, pkgDescription = ?, pkgVersion = ? where pkgID = ?", $v);
        }
    }

    /**
     * Upgrades a package's database and refreshes all blocks.
     */
    public function upgrade()
    {
        $this->upgradeDatabase();

        // now we refresh all blocks
        $items = $this->getPackageItems();
        if (is_array($items['block_types'])) {
            foreach ($items['block_types'] as $item) {
                $item->refresh();
            }
        }
        Localization::clearCache();
    }

    /**
     * Updates a package's database using entities and a db.xml.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function upgradeDatabase()
    {
        $this->destroyProxyClasses();
        $this->installEntitiesDatabase();

        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
    }

    /**
     * Moves the current package's directory to the trash directory renamed with the package handle and a date code.
     */
    public function backup()
    {
        // you can only backup root level packages.
        // Need to figure something else out for core level
        if ($this->pkgHandle != '' && is_dir(DIR_PACKAGES . '/' . $this->pkgHandle)) {
            $trash = \Config::get('concrete.misc.package_backup_directory');
            if (!is_dir($trash)) {
                mkdir($trash, \Config::get('concrete.filesystem.permissions.directory'));
            }
            $trashName = $trash . '/' . $this->pkgHandle . '_' . date('YmdHis');
            $ret = rename(DIR_PACKAGES . '/' . $this->pkgHandle, $trashName);
            if (!$ret) {
                return array(self::E_PACKAGE_MIGRATE_BACKUP);
            } else {
                $this->backedUpFname = $trashName;
            }
        }
    }

    /**
     * If a package was just backed up by this instance of the package object and the packages/package handle directory doesn't exist, this will restore the
     * package from the trash.
     */
    public function restore()
    {
        if (strlen($this->backedUpFname) && is_dir($this->backedUpFname) && !is_dir(DIR_PACKAGES . '/' . $this->pkgHandle)) {
            return @rename($this->backedUpFname, DIR_PACKAGES . '/' . $this->pkgHandle);
        }

        return false;
    }
}
