<?  defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_StartingPointPackage extends Package {
	

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
		Loader::library('content/importer');
		$this->routines = array(
		new StartingPointInstallRoutine('make_directories', 5, t('Starting installation and creating directories.')),
		new StartingPointInstallRoutine('install_database', 10, t('Creating database tables.')),
		new StartingPointInstallRoutine('add_users', 15, t('Adding admin user.')),
		new StartingPointInstallRoutine('install_permissions', 20, t('Installing permissions & workflow.')),
		new StartingPointInstallRoutine('add_home_page', 23, t('Creating home page.')),
		new StartingPointInstallRoutine('install_attributes', 25, t('Installing attributes.')),
		new StartingPointInstallRoutine('install_blocktypes', 30, t('Adding block types.')),
		new StartingPointInstallRoutine('install_themes', 35, t('Adding themes.')),
		new StartingPointInstallRoutine('install_jobs', 38, t('Installing automated jobs.')),
		new StartingPointInstallRoutine('install_dashboard', 40, t('Installing dashboard.')),
		new StartingPointInstallRoutine('install_required_single_pages', 50, t('Installing login and registration pages.')),
		new StartingPointInstallRoutine('install_config', 55, t('Configuring site.')),
		new StartingPointInstallRoutine('import_files', 58, t('Importing files.')),
		new StartingPointInstallRoutine('install_content', 65, t('Adding pages and content.')),
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
		Loader::helper('concrete/interface')->cacheInterfaceItems();
	}
	
	public function install_attributes() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/attributes.xml');
	}

	public function install_dashboard() {
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/dashboard.xml');
	}

	public function install_required_single_pages() {
		Loader::model('single_page');
		$ci = new ContentImporter();
		$ci->importContentFile(DIR_BASE_CORE. '/config/install/base/login_registration.xml');
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
			Loader::library('file/importer');
			$fh = new FileImporter();
			$contents = Loader::helper('file')->getDirectoryContents($this->getPackagePath() . '/files');
	
			foreach($contents as $filename) {
				$f = $fh->import($this->getPackagePath() . '/files/' . $filename, $filename);
			}
		}	
	}
	
	public function install_content() {
		Loader::library('content/importer');
		$installDirectory = DIR_BASE_CORE . '/config';
		$ci = new ContentImporter();
		$ci->importContentFile($this->getPackagePath() . '/content.xml');

	}
	

	public function install_database() {
		$db = Loader::db();			
		$installDirectory = DIR_BASE_CORE. '/config';
		try {
			Database::ensureEncoding();
			Package::installDB($installDirectory . '/db.xml');
		} catch (Exception $e) { 
			throw new Exception(t('Unable to install database: %s', $db->ErrorMsg()));
		}
	}

	public function add_users() {
		// insert the default groups
		// create the groups our site users
		// have to add these in the right order so their IDs get set
		// starting at 1 w/autoincrement
		$g1 = Group::add(t("Guest"), t("The guest group represents unregistered visitors to your site."));
		$g2 = Group::add(t("Registered Users"), t("The registered users group represents all user accounts."));
		$g3 = Group::add(t("Administrators"), "");
		
		// insert admin user into the user table
		if (defined('INSTALL_USER_PASSWORD')) {
			$uPassword = INSTALL_USER_PASSWORD;
			$uPasswordEncrypted = User::encryptPassword($uPassword, PASSWORD_SALT);
		} else {
			$uPasswordEncrypted = INSTALL_USER_PASSWORD_HASH;
		}
		$uEmail = INSTALL_USER_EMAIL;
		UserInfo::addSuperUser($uPasswordEncrypted, $uEmail);
		$u = User::getByUserID(USER_SUPER_ID, true, false);
		
		Loader::library('mail/importer');
		MailImporter::add(array('miHandle' => 'private_message'));
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
		if (!is_dir(DIR_FILES_CACHE_DB)) {
			mkdir(DIR_FILES_CACHE_DB, DIRECTORY_PERMISSIONS_MODE);
			chmod(DIR_FILES_CACHE_DB, DIRECTORY_PERMISSIONS_MODE);
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
		
		Loader::model('file_set');
		$fs = FileSet::getGlobal();
		$g1 = Group::getByID(GUEST_GROUP_ID);
		$g2 = Group::getByID(REGISTERED_GROUP_ID);
		$g3 = Group::getByID(ADMIN_GROUP_ID);
		
		$fs->assignPermissions($g1, array('view_file_set_file'));
		$fs->assignPermissions($g3, array('view_file_set_file', 'search_file_set', 'edit_file_set_file_properties', 'edit_file_set_file_contents', 'copy_file_set_files', 'edit_file_set_permissions', 'delete_file_set_files', 'delete_file_set', 'add_file'));
		if (defined('ACTIVE_LOCALE') && ACTIVE_LOCALE != '' && ACTIVE_LOCALE != 'en_US') {
			Config::save('SITE_LOCALE', ACTIVE_LOCALE);
		}
		Config::save('SITE', SITE);
		Config::save('SITE_APP_VERSION', APP_VERSION);
		Config::save('SITE_INSTALLED_APP_VERSION', APP_VERSION);

		$u = new User();
		$u->saveConfig('NEWSFLOW_LAST_VIEWED', 'FIRSTRUN');
		
		$home = Page::getByID(1, "RECENT");
		$home->assignPermissions($g1, array('view_page'));
		$home->assignPermissions($g3, array('view_page_versions', 'view_page_in_sitemap', 'preview_page_as_user', 'edit_page_properties', 'edit_page_contents', 'edit_page_speed_settings', 'edit_page_theme', 'edit_page_type', 'edit_page_permissions', 'delete_page', 'delete_page_versions', 'approve_page_versions', 'add_subpage', 'move_or_copy_page', 'schedule_page_contents_guest_access'));
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
			$availableList[] = Loader::startingPointPackage($pkgHandle);
		}
		return $availableList;
	}
	
}


class Concrete5_Model_StartingPointInstallRoutine {
	
	public function __construct($method, $progress, $text = '') {
		$this->method = $method;
		$this->progress = $progress;
		$this->text = $text;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function getText() {
		return $this->text;
	}

	public function getProgress() {
		return $this->progress;
	}
	
	
}