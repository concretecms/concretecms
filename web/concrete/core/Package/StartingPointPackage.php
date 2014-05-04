<?

namespace Concrete\Core\Package;
use Cache;
use Concrete\Core\Foundation\Object;
use AuthenticationType;
use Loader;
use Package as BasePackage;
use GroupTree;
use Group;
use Page;
use UserInfo;
use User;
use Config;
use FileSet;
use PermissionKey;
use PermissionAccess;
use \Hautelook\Phpass\PasswordHash;

use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use \Concrete\Core\Mail\Importer\MailImporter;
use \Concrete\Core\User\Point\Action\Action as UserPointAction;
use \Concrete\Core\Backup\ContentImporter;
use FileImporter;
class StartingPointPackage extends BasePackage {


	protected $DIR_PACKAGES_CORE = DIR_STARTING_POINT_PACKAGES_CORE;
	protected $DIR_PACKAGES = DIR_STARTING_POINT_PACKAGES;
	protected $REL_DIR_PACKAGES_CORE = REL_DIR_STARTING_POINT_PACKAGES_CORE;
	protected $REL_DIR_PACKAGES = REL_DIR_STARTING_POINT_PACKAGES;

	protected $routines = array();

	public function getInstallRoutines() {
		return $this->routines;
	}

	// default routines

	public function __construct() {
		$this->routines = array(
		new StartingPointInstallRoutine('make_directories', 5, t('Starting installation and creating directories.')),
		new StartingPointInstallRoutine('install_database', 10, t('Creating database tables.')),
		new StartingPointInstallRoutine('add_users', 15, t('Adding admin user.')),
		new StartingPointInstallRoutine('install_permissions', 20, t('Installing permissions & workflow.')),
		new StartingPointInstallRoutine('add_home_page', 23, t('Creating home page.')),
		new StartingPointInstallRoutine('install_attributes', 25, t('Installing attributes.')),
		new StartingPointInstallRoutine('install_blocktypes', 30, t('Adding block types.')),
		new StartingPointInstallRoutine('install_gathering', 33, t('Adding gathering data sources.')),
		new StartingPointInstallRoutine('install_page_types', 36, t('Page type basic setup.')),
		new StartingPointInstallRoutine('install_themes', 38, t('Adding themes.')),
		new StartingPointInstallRoutine('install_jobs', 40, t('Installing automated jobs.')),
		new StartingPointInstallRoutine('install_dashboard', 45, t('Installing dashboard.')),
		new StartingPointInstallRoutine('install_required_single_pages', 55, t('Installing login and registration pages.')),
		new StartingPointInstallRoutine('install_image_editor', 57, t('Adding image editor functionality.')),
		new StartingPointInstallRoutine('install_config', 60, t('Configuring site.')),
		new StartingPointInstallRoutine('import_files', 65, t('Importing files.')),
		new StartingPointInstallRoutine('install_content', 70, t('Adding pages and content.')),
		new StartingPointInstallRoutine('set_site_permissions', 80, t('Setting up site permissions.')),
		new StartingPointInstallRoutine('precache', 85, t('Prefetching information.')),
		new StartingPointInstallRoutine('finish', 95, t('Finishing.'))
		);
	}

	public function add_home_page() {
		Page::addHomePage();
	}

	public function precache() {
		$c = Page::getByPath('/dashboard/home');
		$blocks = $c->getBlocks();
		foreach($blocks as $b) {
			$bi = $b->getInstance();
			$bi->setupAndRun('view');
		}
	 Loader::helper('concrete/ui')->cacheInterfaceItems();
	}

	public function install_attributes() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/attributes.xml');

		$topicType = \Concrete\Core\Tree\TreeType::add('topic', $pkg);
		$topicCategoryNodeType = \Concrete\Core\Tree\Node\NodeType::add('topic_category', $pkg);
		$topicNodeType = \Concrete\Core\Tree\Node\NodeType::add('topic', $pkg);
		$tree = \Concrete\Core\Tree\Type\Topic::add('Topics');

	}

	public function install_dashboard() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/dashboard.xml');
	}

	public function install_gathering() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/gathering.xml');
	}

	public function install_page_types() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/page_types.xml');
	}

	public function install_page_templates() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/page_templates.xml');
	}

	public function install_required_single_pages() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/login_registration.xml');
	}

	public function install_image_editor() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/image_editor.xml');
	}

	public function install_blocktypes() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/blocktypes.xml');
	}

	public function install_themes() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/themes.xml');
	}

	public function install_jobs() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/jobs.xml');
	}

	public function install_config() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/config.xml');
	}

	public function import_files() {
		if (is_dir($this->getPackagePath() . '/files')) {
			$fh = new FileImporter();
			$contents = Loader::helper('file')->getDirectoryContents($this->getPackagePath() . '/files');

			foreach($contents as $filename) {
				$f = $fh->import($this->getPackagePath() . '/files/' . $filename, $filename);
			}
		}
	}

	public function install_content() {
		$installDirectory = DIR_BASE_CORE . '/config';
		$ci = new ContentImporter();
		$ci->importContentFile($this->getPackagePath() . '/content.xml');

	}


	public function install_database() {
		$db = Loader::db();
		$installDirectory = DIR_BASE_CORE. '/config';
		try {
			Package::installDB($installDirectory . '/db.xml');
			$this->indexAdditionalDatabaseFields();
		} catch (Exception $e) {
			throw new Exception(t('Unable to install database: %s', $db->ErrorMsg()));
		}
	}

	protected function indexAdditionalDatabaseFields() {
		$db = Loader::db();

		$db->Execute('alter table PagePaths add index (`cPath` (255))');
		$db->Execute('alter table Groups add index (`gPath` (255))');
		$db->Execute('alter table QueueMessages add FOREIGN KEY (`queue_id`) REFERENCES `Queues` (`queue_id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function add_users() {
		// Firstly, install the core authentication types
		$cba = AuthenticationType::add('concrete', 'concrete5');
		$fba = AuthenticationType::add('facebook', 'facebook');

		$fba->disable();

		\Concrete\Core\Tree\TreeType::add('group');
		\Concrete\Core\Tree\Node\NodeType::add('group');
		$tree = GroupTree::get();
		$tree = GroupTree::add();

		// insert the default groups
		// create the groups our site users
		// specify the ID's since auto increment may not always be +1
		$g1 = Group::add(tc("GroupName", "Guest"), tc("GroupDescription", "The guest group represents unregistered visitors to your site."), false, false, GUEST_GROUP_ID);
		$g2 = Group::add(tc("GroupName", "Registered Users"), tc("GroupDescription", "The registered users group represents all user accounts."), false, false, REGISTERED_GROUP_ID);
		$g3 = Group::add(tc("GroupName", "Administrators"), "", false, false, ADMIN_GROUP_ID);

		// insert admin user into the user table
		if (defined('INSTALL_USER_PASSWORD')) {

			$hasher = new PasswordHash(PASSWORD_HASH_COST_LOG2, PASSWORD_HASH_PORTABLE);
			$uPassword = INSTALL_USER_PASSWORD;
			$uPasswordEncrypted = $hasher->HashPassword($uPassword);
		} else {
			$uPasswordEncrypted = INSTALL_USER_PASSWORD_HASH;
		}
		$uEmail = INSTALL_USER_EMAIL;
		UserInfo::addSuperUser($uPasswordEncrypted, $uEmail);
		$u = User::getByUserID(USER_SUPER_ID, true, false);

		MailImporter::add(array('miHandle' => 'private_message'));
		UserPointAction::add('won_badge', t('Won a Badge'), 5, false, true);
	}

	public function make_directories() {
		Cache::flush();

		if (!is_dir(DIR_FILES_UPLOADED_THUMBNAILS)) {
			mkdir(DIR_FILES_UPLOADED_THUMBNAILS, DIRECTORY_PERMISSIONS_MODE);
			chmod(DIR_FILES_UPLOADED_THUMBNAILS, DIRECTORY_PERMISSIONS_MODE);
		}
		if (!is_dir(DIR_FILES_INCOMING)) {
			mkdir(DIR_FILES_INCOMING, DIRECTORY_PERMISSIONS_MODE);
			chmod(DIR_FILES_INCOMING, DIRECTORY_PERMISSIONS_MODE);
		}
		if (!is_dir(DIR_FILES_TRASH)) {
			mkdir(DIR_FILES_TRASH, DIRECTORY_PERMISSIONS_MODE);
			chmod(DIR_FILES_TRASH, DIRECTORY_PERMISSIONS_MODE);
		}
		if (!is_dir(DIR_FILES_CACHE)) {
			mkdir(DIR_FILES_CACHE, DIRECTORY_PERMISSIONS_MODE);
			chmod(DIR_FILES_CACHE, DIRECTORY_PERMISSIONS_MODE);
		}
		if (!is_dir(DIR_FILES_AVATARS)) {
			mkdir(DIR_FILES_AVATARS, DIRECTORY_PERMISSIONS_MODE);
			chmod(DIR_FILES_AVATARS, DIRECTORY_PERMISSIONS_MODE);
		}
	}

	public function finish() {
		rename(DIR_CONFIG_SITE . '/site_install.php', DIR_CONFIG_SITE . '/site.php');
		@unlink(DIR_CONFIG_SITE . '/site_install_user.php');
		// remove this line and uncomment the two above when done developing !!
		//copy(DIR_CONFIG_SITE . '/site_install.php', DIR_CONFIG_SITE . '/site.php');
		@chmod(DIR_CONFIG_SITE . '/site.php', FILE_PERMISSIONS_MODE);
		Cache::flush();

	}

	public function install_permissions() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/permissions.xml');
	}

	public function set_site_permissions() {

		$fs = FileSet::getGlobal();
		$g1 = Group::getByID(GUEST_GROUP_ID);
		$g2 = Group::getByID(REGISTERED_GROUP_ID);
		$g3 = Group::getByID(ADMIN_GROUP_ID);

		$fs->assignPermissions($g1, array('view_file_set_file'));
		$fs->assignPermissions($g3, array('view_file_set_file', 'search_file_set', 'edit_file_set_file_properties', 'edit_file_set_file_contents', 'copy_file_set_files', 'edit_file_set_permissions', 'delete_file_set_files', 'delete_file_set', 'add_file'));
		if (defined('SITE_INSTALL_LOCALE') && SITE_INSTALL_LOCALE != '' && SITE_INSTALL_LOCALE != 'en_US') {
			Config::save('SITE_LOCALE', SITE_INSTALL_LOCALE);
		}
		Config::save('SITE', SITE);
		Config::save('SITE_APP_VERSION', APP_VERSION);
		Config::save('SITE_INSTALLED_APP_VERSION', APP_VERSION);

		$u = new User();
		$u->saveConfig('NEWSFLOW_LAST_VIEWED', 'FIRSTRUN');

		$home = Page::getByID(1, "RECENT");
		$home->assignPermissions($g1, array('view_page'));
		$home->assignPermissions($g3, array('view_page_versions', 'view_page_in_sitemap', 'preview_page_as_user', 'edit_page_properties', 'edit_page_contents', 'edit_page_speed_settings', 'edit_page_theme', 'edit_page_template', 'edit_page_permissions', 'delete_page', 'delete_page_versions', 'approve_page_versions', 'add_subpage', 'move_or_copy_page', 'schedule_page_contents_guest_access'));

		// dashboard
		$dashboard = Page::getByPath('/dashboard', "RECENT");
		$dashboard->assignPermissions($g3, array('view_page'));

		Config::save('SECURITY_TOKEN_JOBS', Loader::helper('validation/identifier')->getString(64));
		Config::save('SECURITY_TOKEN_ENCRYPTION', Loader::helper('validation/identifier')->getString(64));
		Config::save('SECURITY_TOKEN_VALIDATION', Loader::helper('validation/identifier')->getString(64));

		// group permissions
		$tree = GroupTree::get();
		$node = $tree->getRootTreeNodeObject();
		$permissions = array('search_users_in_group', 'edit_group', 'assign_group', 'add_sub_group', 'edit_group_permissions');
		$adminGroupEntity = GroupPermissionAccessEntity::getOrCreate($g3);
		foreach($permissions as $pkHandle) {
			$pk = PermissionKey::getByHandle($pkHandle);
			$pk->setPermissionObject($node);
			$pa = PermissionAccess::create($pk);
			$pa->addListItem($adminGroupEntity);
			$pt = $pk->getPermissionAssignmentObject();
			$pt->assignPermissionAccess($pa);
		}
	}

	public static function hasCustomList() {
		$fh = Loader::helper('file');
		if (is_dir(DIR_STARTING_POINT_PACKAGES)) {
			$available = $fh->getDirectoryContents(DIR_STARTING_POINT_PACKAGES);
			if (count($available) > 0) {
				return true;
			}
		}
		return false;
	}

	public static function getClass($pkgHandle) {
		$dir = (is_dir(DIR_STARTING_POINT_PACKAGES . '/' . $pkgHandle)) ? DIR_STARTING_POINT_PACKAGES : DIR_STARTING_POINT_PACKAGES_CORE;
		if (file_exists($dir . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER)) {
			require_once($dir . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER);
			$class = Object::camelcase($pkgHandle) . "StartingPointPackage";
			if (class_exists($class)) {
				$cl = new $class;
				return $cl;
			}
		}
	}

	public static function getAvailableList() {
		$fh = Loader::helper('file');
		// first we check the root install directory. If it exists, then we only include stuff from there. Otherwise we get it from the core.
		$available = array();
		if (is_dir(DIR_STARTING_POINT_PACKAGES)) {
			$available = $fh->getDirectoryContents(DIR_STARTING_POINT_PACKAGES);
		}
		if (count($available) == 0) {
			$available = $fh->getDirectoryContents(DIR_STARTING_POINT_PACKAGES_CORE);
		}
		$availableList = array();
		foreach($available as $pkgHandle) {
			$availableList[] = static::getClass($pkgHandle);
		}
		return $availableList;
	}

}
