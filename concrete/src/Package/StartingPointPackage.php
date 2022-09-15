<?php
namespace Concrete\Core\Package;

use AuthenticationType;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\Api\OAuth\Scope\ScopeRegistryInterface;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Config\Renderer;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Service\File;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Mail\Importer\MailImporter;
use Concrete\Core\Package\Routine\AttachModeInstallRoutine;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Access\Entity\ConversationMessageAuthorEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Updater\Migrations\Configuration;
use Concrete\Core\User\Group\FolderManager;
use Config;
use Core;
use Database;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Group;
use GroupTree;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Package as BasePackage;
use Page;
use PermissionKey;
use Throwable;
use Concrete\Core\User\User;
use UserInfo;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Application\Application;

class StartingPointPackage extends Package
{
    protected $DIR_PACKAGES_CORE = DIR_STARTING_POINT_PACKAGES_CORE;
    protected $DIR_PACKAGES = DIR_STARTING_POINT_PACKAGES;
    protected $REL_DIR_PACKAGES_CORE = REL_DIR_STARTING_POINT_PACKAGES_CORE;
    protected $REL_DIR_PACKAGES = REL_DIR_STARTING_POINT_PACKAGES;

    protected $routines = [];

    /**
     * @var InstallerOptions|null
     */
    protected $installerOptions = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->routines = [
            new StartingPointInstallRoutine('make_directories', 5, t('Starting installation and creating directories.')),
            new StartingPointInstallRoutine('install_database', 19, t('Creating database tables.')),
            new StartingPointInstallRoutine('install_site', 20, t('Creating site.')),
            new StartingPointInstallRoutine('add_users', 21, t('Adding admin user.')),
            new StartingPointInstallRoutine('install_permissions', 22, t('Installing permissions & workflow.')),
            new StartingPointInstallRoutine('install_data_objects', 23, t('Installing Custom Data Objects.')),
            new StartingPointInstallRoutine('add_home_page', 24, t('Creating home page.')),
            new StartingPointInstallRoutine('install_attributes', 25, t('Installing attributes.')),
            new StartingPointInstallRoutine('install_blocktypes_basic', 38, t('Adding Basic block types.')),
            new StartingPointInstallRoutine('install_blocktypes_navigation', 42, t('Adding Navigation block types.')),
            new StartingPointInstallRoutine('install_blocktypes_form', 45, t('Adding Form block types.')),
            new StartingPointInstallRoutine('install_blocktypes_express', 48, t('Adding Express block types.')),
            new StartingPointInstallRoutine('install_blocktypes_social', 51, t('Adding Social block types.')),
            new StartingPointInstallRoutine('install_blocktypes_calendar', 54, t('Adding Calendar block types.')),
            new StartingPointInstallRoutine('install_blocktypes_multimedia', 57, t('Adding Multimedia block types.')),
            new StartingPointInstallRoutine('install_blocktypes_core_desktop', 61, t('Adding Desktop block types.')),
            new StartingPointInstallRoutine('install_blocktypes_other', 62, t('Adding other block types.')),
            new StartingPointInstallRoutine('install_boards', 64, t('Adding boards.')),
            new StartingPointInstallRoutine('install_page_types', 65, t('Page type basic setup.')),
            new StartingPointInstallRoutine('install_tasks', 66, t('Installing tasks.')),
            new StartingPointInstallRoutine('install_dashboard', 69, t('Installing dashboard.')),
            new StartingPointInstallRoutine('install_required_single_pages', 75, t('Installing login and registration pages.')),
            new StartingPointInstallRoutine('install_config', 78, t('Configuring site.')),
            new StartingPointInstallRoutine('install_themes', 79, t('Adding themes.')),
            new StartingPointInstallRoutine('install_file_manager', 80, t('Installing file manager.')),
            new StartingPointInstallRoutine('import_files', 82, t('Importing files.')),
            new StartingPointInstallRoutine('install_content', 86, t('Adding pages and content.')),
            new StartingPointInstallRoutine('install_desktops', 92, t('Adding desktops.')),
            new StartingPointInstallRoutine('install_api', 93, t('Installing API.')),
            new StartingPointInstallRoutine('install_site_permissions', 94, t('Setting site permissions.')),
            new AttachModeInstallRoutine('finish', 95, t('Finishing.')),
        ];
    }

    public function setInstallerOptions(InstallerOptions $installerOptions = null)
    {
        $this->installerOptions = $installerOptions;
    }

    // default routines

    public static function hasCustomList()
    {
        $fh = Core::make('helper/file');
        if (is_dir(DIR_STARTING_POINT_PACKAGES)) {
            $available = $fh->getDirectoryContents(DIR_STARTING_POINT_PACKAGES);
            if (count($available) > 0) {
                return true;
            }
        }

        return false;
    }

    public static function getAvailableList()
    {
        $fh = Core::make('helper/file');
        // first we check the root install directory. If it exists, then we only include stuff from there. Otherwise we get it from the core.
        $available = [];
        if (is_dir(DIR_STARTING_POINT_PACKAGES)) {
            $available = $fh->getDirectoryContents(DIR_STARTING_POINT_PACKAGES);
        }
        if (count($available) == 0) {
            $available = $fh->getDirectoryContents(DIR_STARTING_POINT_PACKAGES_CORE);
        }
        $availableList = [];
        foreach ($available as $pkgHandle) {
            $cl = static::getClass($pkgHandle);
            if ($cl !== null) {
                $availableList[] = $cl;
            }
        }

        return $availableList;
    }

    /**
     * @param string $pkgHandle
     *
     * @return static|null
     */
    public static function getClass($pkgHandle)
    {
        if (is_dir(DIR_STARTING_POINT_PACKAGES . '/' . $pkgHandle)) {
            $class = '\\Application\\StartingPointPackage\\' . camelcase($pkgHandle) . '\\Controller';
        } else {
            $class = '\\Concrete\\StartingPointPackage\\' . camelcase($pkgHandle) . '\\Controller';
        }
        if (class_exists($class, true)) {
            $cl = Core::build($class);
        } else {
            $cl = null;
        }

        return $cl;
    }

    /**
     * @return StartingPointInstallRoutine[]
     */
    public function getInstallRoutines()
    {
        return $this->routines;
    }

    /**
     * @param string $routineName
     *
     * @throws Exception
     * @throws Throwable
     */
    public function executeInstallRoutine($routineName)
    {
        if (!@ini_get('safe_mode') && $this->app->make(FunctionInspector::class)->functionAvailable('set_time_limit')) {
            @set_time_limit(1000);
        }
        $timezone = $this->installerOptions->getServerTimeZone(true);
        date_default_timezone_set($timezone->getName());
        $this->app->make('config')->set('app.server_timezone', $timezone->getName());
        $localization = Localization::getInstance();
        $localization->pushActiveContext(Localization::CONTEXT_SYSTEM);
        $error = null;
        try {
            $this->$routineName();
        } catch (Exception $x) {
            $error = $x;
        } catch (Throwable $x) {
            $error = $x;
        }
        $localization->popActiveContext();
        if ($error !== null) {
            throw $error;
        }
    }

    protected function add_home_page()
    {
        Page::addHomePage();
    }

    protected function install_data_objects()
    {
        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_category');
        \Concrete\Core\Tree\TreeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_site_results');

        $tree = ExpressEntryResults::add();
        $node = $tree->getRootTreeNodeObject();

        // Add forms node beneath it.
        $forms = ExpressEntryCategory::add(ExpressFormBlockController::FORM_RESULTS_CATEGORY_NAME, $node);

        // Set the forms node to allow guests to post entries, since we're using it from the front-end.
        $forms->assignPermissions(
            Group::getByID(GUEST_GROUP_ID),
            ['add_express_entries']
        );

        // Set the root node to allow guests to view entries, so that blocks like express
        // entry list and express entry details work.
        $node->assignPermissions(
            Group::getByID(GUEST_GROUP_ID),
            ['view_express_entries']
        );
    }

    protected function install_attributes()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/attributes.xml');

        $topicType = \Concrete\Core\Tree\TreeType::add('topic');
        $topicNodeType = \Concrete\Core\Tree\Node\NodeType::add('topic');
    }

    protected function install_dashboard()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/single_pages/dashboard.xml');
    }

    protected function install_boards()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/boards.xml');
    }

    protected function install_page_types()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/page_types.xml');
    }

    protected function install_required_single_pages()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/single_pages/global.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/single_pages/root.xml');
    }

    /**
     * @deprecated This method has been splitted in smaller chunks
     */
    protected function install_blocktypes()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_basic.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_navigation.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_form.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_express.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_social.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_calendar.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_multimedia.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_core_desktop.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_other.xml');
    }

    protected function install_blocktypes_basic()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_basic.xml');
    }

    protected function install_blocktypes_navigation()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_navigation.xml');
    }

    protected function install_blocktypes_form()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_form.xml');
    }

    protected function install_blocktypes_express()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_express.xml');
    }

    protected function install_blocktypes_social()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_social.xml');
    }

    protected function install_blocktypes_calendar()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_calendar.xml');
    }

    protected function install_blocktypes_multimedia()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_multimedia.xml');
    }

    protected function install_blocktypes_core_desktop()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_core_desktop.xml');
    }

    protected function install_blocktypes_other()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/blocktypes_other.xml');
    }

    protected function install_themes()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/summary.xml');
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/themes.xml');
        if (file_exists($this->getPackagePath() . '/themes.xml')) {
            $ci->importContentFile($this->getPackagePath() . '/themes.xml');
        }
    }

    protected function install_tasks()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/tasks.xml');
    }

    protected function install_config()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/config.xml');
    }

    protected function install_file_manager()
    {
        $type = \Concrete\Core\File\StorageLocation\Type\Type::add('default', t('Default'));
        \Concrete\Core\File\StorageLocation\Type\Type::add('local', t('Local'));
        $configuration = $type->getConfigurationObject();
        $fsl = \Concrete\Core\File\StorageLocation\StorageLocation::add($configuration, t('Default'), true);

        $filesystem = new Filesystem();
        $tree = $filesystem->create();
        $filesystem->setDefaultPermissions($tree);

        $thumbnailType = new \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type();
        $thumbnailType->requireType();
        $thumbnailType->setName(tc('ThumbnailTypeName', 'File Manager Thumbnails'));
        $thumbnailType->setHandle(Config::get('concrete.icons.file_manager_listing.handle'));
        $thumbnailType->setSizingMode($thumbnailType::RESIZE_EXACT);
        $thumbnailType->setIsUpscalingEnabled(true);
        $thumbnailType->setWidth(Config::get('concrete.icons.file_manager_listing.width'));
        $thumbnailType->setHeight(Config::get('concrete.icons.file_manager_listing.height'));
        $thumbnailType->save();

        $thumbnailType = new \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type();
        $thumbnailType->requireType();
        $thumbnailType->setName(tc('ThumbnailTypeName', 'File Manager Detail Thumbnails'));
        $thumbnailType->setHandle(Config::get('concrete.icons.file_manager_detail.handle'));
        $thumbnailType->setSizingMode($thumbnailType::RESIZE_EXACT);
        $thumbnailType->setIsUpscalingEnabled(false);
        $thumbnailType->setWidth(Config::get('concrete.icons.file_manager_detail.width'));
        $thumbnailType->setHeight(Config::get('concrete.icons.file_manager_detail.height'));
        $thumbnailType->save();
    }

    protected function import_files()
    {
        if (is_dir($this->getPackagePath() . '/files')) {
            $ch = new ContentImporter();
            $computeThumbnails = true;
            if ($this->contentProvidesFileThumbnails()) {
                $computeThumbnails = false;
            }
            $ch->importFiles($this->getPackagePath() . '/files', $computeThumbnails);
        }
    }

    protected function install_content()
    {
        $ci = new ContentImporter();
        $ci->importContentFile($this->getPackagePath() . '/content.xml');
    }

    protected function install_desktops()
    {
        $desktop = \Page::getByPath('/dashboard/welcome');
        $desktop->movePageDisplayOrderToTop();
    }

    protected function install_api()
    {
        $scopes = $this->app->make(ScopeRegistryInterface::class)->getScopes();
        $em = $this->app->make(EntityManager::class);
        foreach ($scopes as $scope) {
            $em->persist($scope);
            $em->flush();
        }
    }

    protected function install_database()
    {
        $db = Database::get();
        $num = $db->GetCol('show tables');

        if (count($num) > 0) {
            throw new \Exception(
                t(
                    'There are already %s tables in this database. Concrete must be installed in an empty database.',
                    count($num)));
        }
        $installDirectory = DIR_BASE_CORE . '/config';
        try {
            // Retrieving metadata from the entityManager created with \ORM::entityManager()
            // will result in a empty metadata array. Because all drivers are wrapped in a driverChain
            // the method getAllMetadata() of Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory
            // is going to return a empty array. To overcome this issue a new EntityManager is create with the
            // only purpose to be used during the installation.
            $config = Setup::createConfiguration(true, \Config::get('database.proxy_classes'));
            \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('subpackages');
            \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('package');
            // Use default AnnotationReader
            $driverImpl = $config->newDefaultAnnotationDriver(DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES, false);
            $config->setMetadataDriverImpl($driverImpl);
            $em = EntityManager::create(\Database::connection(), $config);
            $dbm = new DatabaseStructureManager($em);
            $dbm->destroyProxyClasses();
            $dbm->generateProxyClasses();

            Package::installDB($installDirectory . '/db.xml');

            $dbm->installDatabase();
            $this->indexAdditionalDatabaseFields();

            $configuration = new Configuration();
            $version = $configuration->getVersion(Config::get('concrete.version_db'));
            $version->markMigrated();
            $configuration->registerPreviousMigratedVersions();
        } catch (\Exception $e) {
            throw new \Exception(t('Unable to install database: %s', $e->getMessage()));
        }
    }

    protected function indexAdditionalDatabaseFields()
    {
        $db = Database::get();

        $textIndexes = $this->app->make('config')->get('database.text_indexes');
        $db->createTextIndexes($textIndexes);
    }

    protected function add_users()
    {
        // Firstly, install the core authentication types
        $cba = AuthenticationType::add('concrete', 'Standard');
        $coa = AuthenticationType::add('community', 'community.concretecms.com');
        $fba = AuthenticationType::add('facebook', 'Facebook');
        $twa = AuthenticationType::add('twitter', 'Twitter');
        $gat = AuthenticationType::add('google', 'Google');
        $ext = AuthenticationType::add('external_concrete', 'External Concrete Site');

        $fba->disable();
        $twa->disable();
        $coa->disable();
        $gat->disable();
        $ext->disable();

        \Concrete\Core\Tree\TreeType::add('group');
        \Concrete\Core\Tree\Node\NodeType::add('group');
        $tree = GroupTree::get();
        $tree = GroupTree::add();

        // insert the default groups
        // create the groups our site users
        // specify the ID's since auto increment may not always be +1
        $g1 = Group::add(
            tc('GroupName', 'Guest'),
            tc('GroupDescription', 'The guest group represents unregistered visitors to your site.'),
            false,
            false,
            GUEST_GROUP_ID);
        $g2 = Group::add(
            tc('GroupName', 'Registered Users'),
            tc('GroupDescription', 'The registered users group represents all user accounts.'),
            false,
            false,
            REGISTERED_GROUP_ID);
        $g3 = Group::add(tc('GroupName', 'Administrators'), '', false, false, ADMIN_GROUP_ID);

        $superuser = UserInfo::addSuperUser($this->installerOptions->getUserPasswordHash(), $this->installerOptions->getUserEmail());
        $u = User::getByUserID(USER_SUPER_ID, true, false);

        MailImporter::add(['miHandle' => 'private_message']);

        // Install conversation default email
        \Conversation::setDefaultSubscribedUsers([$superuser]);
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/conversation.xml');

        $folderManager = new FolderManager();
        $folderManager->create();

        // Add Group Type + Default Role and assign them to the groups
        $db = Database::get();
        $db->executeQuery('insert into GroupTypes (gtID, gtName, gtDefaultRoleID) values (?,?, ?)', [DEFAULT_GROUP_TYPE_ID, t("Group"), DEFAULT_GROUP_ROLE_ID]);
        $db->executeQuery('insert into GroupRoles (grID, grName) values (?,?)', [DEFAULT_GROUP_ROLE_ID, t("Member")]);
        $db->executeQuery('insert into GroupTypeSelectedRoles (gtID, grID) values (?,?)', [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]);
        $db->executeQuery('update `Groups` set gtID = ?, gDefaultRoleID = ?', [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]);
        $db->executeQuery('update UserGroups set grID = ?', [DEFAULT_GROUP_ROLE_ID]);
    }

    protected function make_directories()
    {
        // Delete generated overrides and doctrine
        $fh = new File();
        if (is_dir(DIR_CONFIG_SITE . '/generated_overrides')) {
            $fh->removeAll(DIR_CONFIG_SITE . '/generated_overrides');
        }
        if (is_dir(Config::get('database.proxy_classes'))) {
            $fh->removeAll(Config::get('database.proxy_classes'));
        }

        if (!is_dir(Config::get('concrete.cache.directory'))) {
            mkdir(Config::get('concrete.cache.directory'), Config::get('concrete.filesystem.permissions.directory'));
            chmod(Config::get('concrete.cache.directory'), Config::get('concrete.filesystem.permissions.directory'));
        }

        if (!is_dir(DIR_FILES_UPLOADED_STANDARD . REL_DIR_FILES_INCOMING)) {
            mkdir(
                DIR_FILES_UPLOADED_STANDARD . REL_DIR_FILES_INCOMING,
                Config::get('concrete.filesystem.permissions.directory'));
            chmod(
                DIR_FILES_UPLOADED_STANDARD . REL_DIR_FILES_INCOMING,
                Config::get('concrete.filesystem.permissions.directory'));
        }
    }

    protected function finish()
    {
        $config = $this->app->make('config');
        $installConfiguration = $this->installerOptions->getConfiguration();

        // Extract database config, and save it to database.php
        $database = $installConfiguration['database'];
        unset($installConfiguration['database']);

        $renderer = new Renderer($database);

        file_put_contents(DIR_CONFIG_SITE . '/database.php', $renderer->render());
        @chmod(DIR_CONFIG_SITE . '/database.php', $config->get('concrete.filesystem.permissions.file'));

        $siteConfig = \Site::getDefault()->getConfigRepository();
        if (isset($installConfiguration['canonical-url']) && $installConfiguration['canonical-url']) {
            $siteConfig->save('seo.canonical_url', $installConfiguration['canonical-url']);
        }
        unset($installConfiguration['canonical-url']);
        if (isset($installConfiguration['canonical-url-alternative']) && $installConfiguration['canonical-url-alternative']) {
            $siteConfig->save('seo.canonical_url_alternative', $installConfiguration['canonical-url-alternative']);
        }
        unset($installConfiguration['canonical-url-alternative']);

        if (isset($installConfiguration['session-handler']) && $installConfiguration['session-handler']) {
            $config->save('concrete.session.handler', $installConfiguration['session-handler']);
        }
        unset($installConfiguration['session-handler']);

        $renderer = new Renderer($installConfiguration);
        if (!file_exists(DIR_CONFIG_SITE . '/app.php')) {
            file_put_contents(DIR_CONFIG_SITE . '/app.php', $renderer->render());
            @chmod(DIR_CONFIG_SITE . '/app.php', $config->get('concrete.filesystem.permissions.file'));
        }
        $config->save('app.server_timezone', $this->installerOptions->getServerTimeZone(true)->getName());

        $this->installerOptions->deleteFiles();

        // Set the version_db as the version_db_installed
        $config->save('concrete.version_db_installed', $config->get('concrete.version_db'));

        // Clear cache
        $config->clearCache();
        $this->app->make('cache')->flush();
    }

    protected function install_permissions()
    {
        $ci = new ContentImporter();
        $ci->importContentFile(DIR_BASE_CORE . '/config/install/base/permissions.xml');
    }

    protected function install_site()
    {
        \Core::make('site/type')->installDefault();
        $site = \Site::installDefault($this->installerOptions->getSiteLocaleId());
        $site->getConfigRepository()->save('name', $this->installerOptions->getSiteName());

        $uiLocaleId = $this->installerOptions->getUiLocaleId();
        if ($uiLocaleId && $uiLocaleId !== Localization::BASE_LOCALE) {
            Config::save('concrete.locale', $uiLocaleId);
        }

        Config::save('concrete.version_installed', APP_VERSION);
        Config::save('concrete.misc.login_redirect', 'HOMEPAGE');

        $dbConfig = \Core::make('config/database');
        $dbConfig->save('app.privacy_policy_accepted', $this->installerOptions->isPrivacyPolicyAccepted());
    }

    protected function install_site_permissions()
    {
        $g1 = Group::getByID(GUEST_GROUP_ID);
        $g2 = Group::getByID(REGISTERED_GROUP_ID);
        $g3 = Group::getByID(ADMIN_GROUP_ID);

        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        $folder->assignPermissions($g1, ['view_file_folder_file']);
        $folder->assignPermissions(
            $g3,
            [
                'view_file_folder_file',
                'search_file_folder',
                'edit_file_folder',
                'edit_file_folder_file_properties',
                'edit_file_folder_file_contents',
                'copy_file_folder_files',
                'edit_file_folder_permissions',
                'delete_file_folder_files',
                'delete_file_folder',
                'add_file',
            ]
        );

        // login
        $login = Page::getByPath('/login', 'RECENT');
        $login->assignPermissions($g1, ['view_page']);

        // register
        $register = Page::getByPath('/register', 'RECENT');
        $register->assignPermissions($g1, ['view_page']);

        // Page Forbidden
        $page_forbidden = Page::getByPath('/page_forbidden', "RECENT");
        $page_forbidden->assignPermissions($g1, ['view_page']);

        // Page Not Found
        $page_not_found = Page::getByPath('/page_not_found', "RECENT");
        $page_not_found->assignPermissions($g1, ['view_page']);

        // dashboard
        $dashboard = Page::getByPath('/dashboard', 'RECENT');
        $dashboard->assignPermissions($g3, ['view_page']);

        // drafts
        $drafts = Page::getByPath('/!drafts', 'RECENT');
        $drafts->assignPermissions(
            $g3,
            [
                'view_page',
                'view_page_versions',
                'view_page_in_sitemap',
                'preview_page_as_user',
                'edit_page_properties',
                'edit_page_contents',
                'edit_page_speed_settings',
                'edit_page_multilingual_settings',
                'edit_page_theme',
                'edit_page_template',
                'edit_page_page_type',
                'edit_page_permissions',
                'delete_page',
                'delete_page_versions',
                'approve_page_versions',
                'add_subpage',
                'move_or_copy_page',
                'schedule_page_contents_guest_access',
            ]
        );

        $home = Page::getByID(Page::getHomePageID(), 'RECENT');
        $home->assignPermissions($g1, ['view_page']);
        $home->assignPermissions(
            $g3,
            [
                'view_page_versions',
                'view_page_in_sitemap',
                'preview_page_as_user',
                'edit_page_properties',
                'edit_page_contents',
                'edit_page_speed_settings',
                'edit_page_multilingual_settings',
                'edit_page_theme',
                'edit_page_template',
                'edit_page_page_type',
                'edit_page_permissions',
                'delete_page',
                'delete_page_versions',
                'approve_page_versions',
                'add_subpage',
                'move_or_copy_page',
                'schedule_page_contents_guest_access',
            ]
        );

        $config = \Core::make('config/database');
        $config->save('concrete.security.token.jobs', Core::make('helper/validation/identifier')->getString(64));
        $config->save('concrete.security.token.encryption', Core::make('helper/validation/identifier')->getString(64));
        $config->save('concrete.security.token.validation', Core::make('helper/validation/identifier')->getString(64));

        // group permissions
        $tree = GroupTree::get();
        $node = $tree->getRootTreeNodeObject();
        $permissions = [
            'search_group_folder',
            'edit_group_folder',
            'edit_group_folder_permissions',
            'delete_group_folder',
            'add_group',
            'assign_groups',
            'add_group_folder',
        ];
        $adminGroupEntity = GroupPermissionAccessEntity::getOrCreate($g3);
        foreach ($permissions as $pkHandle) {
            $pk = PermissionKey::getByHandle($pkHandle);
            $pk->setPermissionObject($node);
            $pa = PermissionAccess::create($pk);
            $pa->addListItem($adminGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }

        // conversation permissions
        $messageAuthorEntity = ConversationMessageAuthorEntity::getOrCreate();
        $guestEntity = GroupPermissionAccessEntity::getOrCreate($g1);
        $registeredEntity = GroupPermissionAccessEntity::getOrCreate($g2);

        $pk = PermissionKey::getByHandle('add_conversation_message');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($guestEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = PermissionKey::getByHandle('add_conversation_message_attachments');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($guestEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = PermissionKey::getByHandle('edit_conversation_message');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($messageAuthorEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = PermissionKey::getByHandle('delete_conversation_message');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($messageAuthorEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = PermissionKey::getByHandle('rate_conversation_message');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($registeredEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $permissions = [
            'edit_conversation_permissions',
            'flag_conversation_message',
            'approve_conversation_message',
        ];
        foreach ($permissions as $pkHandle) {
            $pk = PermissionKey::getByHandle($pkHandle);
            $pa = PermissionAccess::create($pk);
            $pa->addListItem($adminGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }

        // notification
        $adminUserEntity = UserEntity::getOrCreate(\UserInfo::getByID(USER_SUPER_ID));
        $pk = PermissionKey::getByHandle('notify_in_notification_center');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($adminUserEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        try {
            Core::make('helper/file')->makeExecutable(DIR_BASE_CORE . '/bin/concrete', 'all');
        } catch (\Exception $x) {
        }
    }
}
