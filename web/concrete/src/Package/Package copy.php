<?php
namespace Concrete\Core\Package;

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
class Packagederp extends Object
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
     * @var \Concrete\Core\Database\DatabaseStructureManager
     */
    protected $databaseStructureManager;
    protected $appVersionRequired = '5.7.0';
    protected $pkgAllowsFullContentSwap = false;
    protected $pkgContentProvidesFileThumbnails = false;


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

        //$items['attribute_categories'] = AttributeKeyCategory::getListByPackage($this);
        $items['permission_categories'] = PermissionKeyCategory::getListByPackage($this);
        $items['permission_access_entity_types'] = PermissionAccessEntityType::getListByPackage($this);
        //$items['attribute_keys'] = AttributeKey::getListByPackage($this);
        //$items['attribute_sets'] = AttributeSet::getListByPackage($this);
        $items['group_sets'] = GroupSet::getListByPackage($this);
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
        $items['permissions'] = PermissionKey::getListByPackage($this);
        $items['single_pages'] = SinglePage::getListByPackage($this);
        //$items['attribute_types'] = AttributeType::getListByPackage($this);
        $items['captcha_libraries'] = SystemCaptchaLibrary::getListByPackage($this);
        $items['content_editor_snippets'] = SystemContentEditorSnippet::getListByPackage($this);
        $items['conversation_editors'] = ConversationEditor::getListByPackage($this);
        $items['conversation_rating_types'] = ConversationRatingType::getListByPackage($this);
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
