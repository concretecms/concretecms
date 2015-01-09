<?php
namespace Concrete\Core\Package;

use Concrete\Core\Authentication\AuthenticationType as AuthenticationType;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\File\FileList;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Sharing\SocialNetwork\Link;
use Concrete\Core\Tree\Type\Topic;
use Page;
use Stack;
use SinglePage;
use UserInfo;
use PageType;
use BlockType;
use Block;
use Group;
use PageTheme;
use Job;
use Core;
use JobSet;
use PageTemplate;
use CollectionAttributeKey;
use \Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\Key\Key as AttributeKey;
use \Concrete\Core\Attribute\Set as AttributeSet;
use GroupSet;
use BlockTypeList;
use \Concrete\Core\Workflow\Type as WorkflowType;
use PermissionKey;
use \Concrete\Core\User\Point\Action\Action as UserPointAction;
use \Concrete\Core\Antispam\Library as SystemAntispamLibrary;
use \Concrete\Core\Mail\Importer\MailImporter;
use PermissionKeyCategory;
use \Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use \Concrete\Core\Workflow\Progress\Category as WorkflowProgressCategory;
use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use PermissionAccess;
use \Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use \Concrete\Core\Editor\Snippet as SystemContentEditorSnippet;
use \Concrete\Core\Feature\Feature;
use \Concrete\Core\Feature\Category\Category as FeatureCategory;
use \Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use \Concrete\Core\Gathering\Item\Template\Template as GatheringItemTemplate;
use \Concrete\Core\Gathering\Item\Template\Type as GatheringItemTemplateType;
use \Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use \Concrete\Core\Conversation\Editor\Editor as ConversationEditor;
use \Concrete\Core\Conversation\Rating\Type as ConversationRatingType;
use \Concrete\Core\ImageEditor\ControlSet as SystemImageEditorControlSet;
use \Concrete\Core\ImageEditor\Filter as SystemImageEditorFilter;
use \Concrete\Core\ImageEditor\Component as SystemImageEditorComponent;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;
use \Concrete\Core\Validation\BannedWord\BannedWord as BannedWord;
use FileImporter;
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Foundation\Object;
use Database;
use \Concrete\Core\Database\Schema\Schema;
use Environment;
use \Concrete\Core\Package\PackageList;
use Localization;
use \Concrete\Core\File\StorageLocation\Type\Type as StorageLocation;

class Package extends Object
{
    protected $DIR_PACKAGES_CORE = DIR_PACKAGES_CORE;
    protected $DIR_PACKAGES = DIR_PACKAGES;
    protected $REL_DIR_PACKAGES_CORE = REL_DIR_PACKAGES_CORE;
    protected $REL_DIR_PACKAGES = REL_DIR_PACKAGES;
    protected $backedUpFname = '';

    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected $config;

    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected $fileConfig;

    public function getRelativePath()
    {
        $dirp = (is_dir($this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->REL_DIR_PACKAGES : $this->REL_DIR_PACKAGES_CORE;

        return $dirp . '/' . $this->pkgHandle;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function getPackageName()
    {
        return t($this->pkgName);
    }

    public function getPackageDescription()
    {
        return t($this->pkgDescription);
    }

    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * Gets the date the package was added to the system,
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getPackageDateInstalled()
    {
        return $this->pkgDateInstalled;
    }

    public function getPackageVersion()
    {
        return $this->pkgVersion;
    }

    public function getPackageVersionUpdateAvailable()
    {
        return $this->pkgAvailableVersion;
    }

    public function isPackageInstalled()
    {
        return $this->pkgIsInstalled;
    }

    public function getChangelogContents()
    {
        if (file_exists($this->getPackagePath() . '/CHANGELOG')) {
            $contents = Core::make('helper/file')->getContents($this->getPackagePath() . '/CHANGELOG');

            return nl2br(Core::make('helper/text')->entities($contents));
        }

        return '';
    }

    /**
     * Returns the currently installed package version.
     * NOTE: This function only returns a value if getLocalUpgradeablePackages() has been called first!
     */
    public function getPackageCurrentlyInstalledVersion()
    {
        return $this->pkgCurrentVersion;
    }

    protected $appVersionRequired = '5.7.0';
    protected $pkgAllowsFullContentSwap = false;

    const E_PACKAGE_NOT_FOUND = 1;
    const E_PACKAGE_INSTALLED = 2;
    const E_PACKAGE_VERSION = 3;
    const E_PACKAGE_DOWNLOAD = 4;
    const E_PACKAGE_SAVE = 5;
    const E_PACKAGE_UNZIP = 6;
    const E_PACKAGE_INSTALL = 7;
    const E_PACKAGE_MIGRATE_BACKUP = 8;
    const E_PACKAGE_INVALID_APP_VERSION = 20;

    protected $errorText = array();

    public function getApplicationVersionRequired()
    {
        return $this->appVersionRequired;
    }

    public function hasInstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install.php');
    }

    public function hasInstallPostScreen()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install_post.php');
    }

    public function allowsFullContentSwap()
    {
        return $this->pkgAllowsFullContentSwap;
    }

    public function showInstallOptionsScreen()
    {
        return $this->hasInstallNotes() || $this->allowsFullContentSwap();
    }

    public static function installDB($xmlFile)
    {
        if (!file_exists($xmlFile)) {
            return false;
        }

        // currently this is just done from xml
        $db = Database::get();

        $parser = Schema::getSchemaParser(simplexml_load_file($xmlFile));
        $parser->setIgnoreExistingTables(false);
        $toSchema = $parser->parse($db);

        $fromSchema = $db->getSchemaManager()->createSchema();
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());

        foreach($saveQueries as $query) {
            $db->query($query);
        }

        /*
        $schema = Database::getADOSChema();
        $sql = $schema->ParseSchema($xmlFile);

        $db->IgnoreErrors($handler);

        if (!$sql) {
            $result->message = $db->ErrorMsg();
            return $result;
        }

        $r = $schema->ExecuteSchema();


        if ($dbLayerErrorMessage != '') {
            $result->message = $dbLayerErrorMessage;
            return $result;
        } if (!$r) {
            $result->message = $db->ErrorMsg();
            return $result;
        }

        $result->result = true;

        $db->CacheFlush();
        */

        $result = new \stdClass();
        $result->result = false;

        return $result;
    }

    public static function getClass($pkgHandle)
    {
        $cache = Core::make('cache/request');
        $item = $cache->getItem('package/class/' . $pkgHandle);
        $cl = $item->get();
        if ($item->isMiss()) {
            $item->lock();
            // loads and instantiates the object
            $class = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Controller';
            try {
                $cl = Core::make($class);
            } catch (\ReflectionException $ex) {
                $cl = new BrokenPackage($pkgHandle);
            }
            $item->set($cl);
        }

        return clone $cl;
    }

    /**
     * Loads package translation files into zend translate
     * @param string $locale = null The identifier of the locale to activate (used to build the language file path). If empty we'll use currently active locale.
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
     * Returns an array of package items (e.g. blocks, themes)
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
        $items['authentication_types'] = AuthenticationType::getListByPackage($this);
        $items['storage_locations'] = StorageLocation::getListByPackage($this);
        ksort($items);

        return $items;
    }

    /** Returns the display name of a category of package items (localized and escaped accordingly to $format)
     * @param string $categoryHandle The category handle
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
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
        } elseif ($item instanceof AttributeKeyCategory) {
            return $txt->unhandle($item->getAttributeKeyCategoryHandle());
        } elseif ($item instanceof AttributeSet) {
            $at = AttributeKeyCategory::getByID($item->getAttributeSetKeyCategoryID());

            return t('%s (%s)', $item->getAttributeSetDisplayName(), $txt->unhandle($at->getAttributeKeyCategoryHandle()));
        } elseif ($item instanceof GroupSet) {
            return $item->getGroupSetDisplayName();
        } elseif ($item instanceof AttributeKey) {
            $akc = AttributeKeyCategory::getByID($item->getAttributeKeyCategoryID());

            return t(' %s (%s)', $txt->unhandle($item->getAttributeKeyHandle()), $txt->unhandle($akc->getAttributeKeyCategoryHandle()));
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
     * Uninstalls the package. Removes any blocks, themes, or pages associated with the package.
     */
    public function uninstall()
    {
        $db = Database::getActiveConnection();

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

        $db->Execute("delete from Packages where pkgID = ?", array($this->pkgID));
        Localization::clearCache();
    }

    protected function validateClearSiteContents($options)
    {
        $u = new \User();
        if ($u->isSuperUser()) {
            // this can ONLY be used through the post. We will use the token to ensure that
            $valt = Core::make('helper/validation/token');
            if ($valt->validate('install_options_selected', $options['ccm_token'])) {
                return true;
            }
        }

        return false;
    }

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
                $fh = new FileImporter();
                $contents = Core::make('helper/file')->getDirectoryContents($this->getPackagePath() . '/content_files');

                foreach ($contents as $filename) {
                    $f = $fh->import($this->getPackagePath() . '/content_files/' . $filename, $filename);
                }
            }

            // now we parse the content.xml if it exists.

            $ci = new ContentImporter();
            $ci->importContentFile($this->getPackagePath() . '/content.xml');

            \Core::make('cache/request')->enable();
        }
    }

    public static function testForInstall($package, $testForAlreadyInstalled = true)
    {
        // this is the pre-test routine that packages run through before they are installed. Any errors that come here
        // are to be returned in the form of an array so we can show the user. If it's all good we return true
        $db = Database::getActiveConnection();
        $errors = array();

        $pkg = static::getClass($package);

        // Step 1 does that package exist ?
        if ((!is_dir(DIR_PACKAGES . '/' . $package) && (!is_dir(DIR_PACKAGES_CORE . '/' . $package))) || $package == '') {
            $errors[] = Package::E_PACKAGE_NOT_FOUND;
        } elseif ($pkg instanceof BrokenPackage) {
            $errors[] = Package::E_PACKAGE_NOT_FOUND;
        }

        // Step 2 - check to see if the user has already installed a package w/this handle
        if ($testForAlreadyInstalled) {
            $cnt = $db->getOne("select count(*) from Packages where pkgHandle = ?", array($package));
            if ($cnt > 0) {
                $errors[] = Package::E_PACKAGE_INSTALLED;
            }
        }

        if (count($errors) == 0) {
            // test minimum application version requirement
            if (version_compare(APP_VERSION, $pkg->getApplicationVersionRequired(), '<')) {
                $errors[] = array(Package::E_PACKAGE_VERSION, $pkg->getApplicationVersionRequired());
            }
        }

        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }

    public function mapError($testResults)
    {
        $errorText[Package::E_PACKAGE_INSTALLED] = t("You've already installed that package.");
        $errorText[Package::E_PACKAGE_NOT_FOUND] = t("Invalid Package.");
        $errorText[Package::E_PACKAGE_VERSION] = t("This package requires concrete5 version %s or greater.");
        $errorText[Package::E_PACKAGE_DOWNLOAD] = t("An error occurred while downloading the package.");
        $errorText[Package::E_PACKAGE_SAVE] = t("concrete5 was not able to save the package after download.");
        $errorText[Package::E_PACKAGE_UNZIP] = t('An error occurred while trying to unzip the package.');
        $errorText[Package::E_PACKAGE_INSTALL] = t('An error occurred while trying to install the package.');
        $errorText[Package::E_PACKAGE_MIGRATE_BACKUP] = t('Unable to backup old package directory to %s', DIR_FILES_TRASH);
        $errorText[Package::E_PACKAGE_INVALID_APP_VERSION] = t('This package isn\'t currently available for this version of concrete5. Please contact the maintainer of this package for assistance.');

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

    /*
     * Returns a path to where the packages files are located.
     * @access public
     * @return string $path
     */

    public function getPackagePath()
    {
        $dirp = (is_dir($this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->DIR_PACKAGES : $this->DIR_PACKAGES_CORE;
        $path = $dirp . '/' . $this->getPackageHandle();

        return $path;
    }

    /**
     * returns a Package object for the given package id, null if not found
     * @param int $pkgID
     * @return Package
     */
    public static function getByID($pkgID)
    {
        $db = Database::getActiveConnection();
        $row = $db->GetRow("select * from Packages where pkgID = ?", array($pkgID));
        if ($row) {
            $pkg = static::getClass($row['pkgHandle']);
            if ($pkg instanceof Package) {
                $pkg->setPropertiesFromArray($row);

                return $pkg;
            }
        }
    }

    /**
     * returns a Package object for the given package handle, null if not found
     * @param string $pkgHandle
     * @return Package
     */
    public static function getByHandle($pkgHandle)
    {
        $db = Database::getActiveConnection();
        $row = $db->GetRow("select * from Packages where pkgHandle = ?", array($pkgHandle));
        if ($row) {
            $pkg = static::getClass($row['pkgHandle']);
            if ($pkg instanceof Package) {
                $pkg->setPropertiesFromArray($row);
            }

            return $pkg;
        }
    }

    /**
     * Called to enable package specific configuration
     */
    public function registerConfigNamespace()
    {
        \Core::make('config')
            ->package($this->getPackageHandle(), $this->getPackagePath() . '/config');
    }

    /**
     * Get the standard database config liaison
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getConfig()
    {
        return $this->getDatabaseConfig();
    }

    /**
     * Get the standard database config liaison
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
     * Get the standard filesystem config liaison
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
     * @return Package
     */
    public function install()
    {
        $cl = \Concrete\Core\Foundation\ClassLoader::getInstance();
        $cl->registerPackage($this);

        PackageList::refreshCache();
        $db = Database::getActiveConnection();
        $dh = Core::make('helper/date');
        $v = array($this->getPackageName(), $this->getPackageDescription(), $this->getPackageVersion(), $this->getPackageHandle(), 1, $dh->getOverridableNow());
        $db->query("insert into Packages (pkgName, pkgDescription, pkgVersion, pkgHandle, pkgIsInstalled, pkgDateInstalled) values (?, ?, ?, ?, ?, ?)", $v);

        $pkg = Package::getByID($db->Insert_ID());
        Package::installDB($pkg->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
        $env = Environment::get();
        $env->clearOverrideCache();
        Localization::clearCache();

        return $pkg;
    }

    public function updateAvailableVersionNumber($vNum)
    {
        $db = Database::getActiveConnection();
        $v = array($vNum, $this->getPackageID());
        $db->query("update Packages set pkgAvailableVersion = ? where pkgID = ?", $v);
    }

    public function upgradeCoreData()
    {
        $db = Database::getActiveConnection();
        $p1 = static::getClass($this->getPackageHandle());
        if ($p1 instanceof Package) {
            $v = array($p1->getPackageName(), $p1->getPackageDescription(), $p1->getPackageVersion(), $this->getPackageID());
            $db->query("update Packages set pkgName = ?, pkgDescription = ?, pkgVersion = ? where pkgID = ?", $v);
        }
    }

    public function upgrade()
    {
        Package::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
        // now we refresh all blocks
        $items = $this->getPackageItems();
        if (is_array($items['block_types'])) {
            foreach ($items['block_types'] as $item) {
                $item->refresh();
            }
        }
        Localization::clearCache();
    }

    public static function getInstalledHandles()
    {
        $db = Database::getActiveConnection();

        return $db->GetCol("select pkgHandle from Packages");
    }

    public static function getInstalledList()
    {
        $db = Database::getActiveConnection();
        $r = $db->query("select * from Packages where pkgIsInstalled = 1 order by pkgDateInstalled asc");
        $pkgArray = array();
        while ($row = $r->fetchRow()) {
            $pkg = new Package();
            $pkg->setPropertiesFromArray($row);

            $pkgArray[] = $pkg;
        }

        return $pkgArray;
    }

    /**
     * Returns an array of packages that have newer versions in the local packages directory
     * than those which are in the Packages table. This means they're ready to be upgraded
     */
    public static function getLocalUpgradeablePackages()
    {
        $packages = Package::getAvailablePackages(false);
        $upgradeables = array();
        $db = Database::getActiveConnection();
        foreach ($packages as $p) {
            $row = $db->GetRow("select pkgID, pkgVersion from Packages where pkgHandle = ? and pkgIsInstalled = 1", array($p->getPackageHandle()));
            if ($row['pkgID'] > 0) {
                if (version_compare($p->getPackageVersion(), $row['pkgVersion'], '>')) {
                    $p->pkgCurrentVersion = $row['pkgVersion'];
                    $upgradeables[] = $p;
                }
            }
        }

        return $upgradeables;
    }

    public static function getRemotelyUpgradeablePackages()
    {
        $packages = Package::getInstalledList();
        $upgradeables = array();
        $db = Database::getActiveConnection();
        foreach ($packages as $p) {
            if (version_compare($p->getPackageVersion(), $p->getPackageVersionUpdateAvailable(), '<')) {
                $upgradeables[] = $p;
            }
        }

        return $upgradeables;
    }

    /**
     * moves the current package's directory to the trash directory renamed with the package handle and a date code.
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
                return array(Package::E_PACKAGE_MIGRATE_BACKUP);
            } else {
                $this->backedUpFname = $trashName;
            }
        }
    }

    /**
     * if a packate was just backed up by this instance of the package object and the packages/package handle directory doesn't exist, this will restore the
     * package from the trash
     */
    public function restore()
    {
        if (strlen($this->backedUpFname) && is_dir($this->backedUpFname) && !is_dir(DIR_PACKAGES . '/' . $this->pkgHandle)) {
            return @rename($this->backedUpFname, DIR_PACKAGES . '/' . $this->pkgHandle);
        }

        return false;
    }

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
}
