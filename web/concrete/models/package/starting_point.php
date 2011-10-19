<?  defined('C5_EXECUTE') or die("Access Denied.");

class StartingPointPackage extends Package {
	

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
		new StartingPointInstallRoutine('make_directories', 10, t('Starting installation and creating directories.')),
		new StartingPointInstallRoutine('install_database', 20, t('Creating database tables.')),
		new StartingPointInstallRoutine('add_users', 45, t('Adding admin user.')),
		new StartingPointInstallRoutine('install_blocks_attributes_permissions_jobs', 50, t('Installing block types, attributes, permissions and jobs.')),
		new StartingPointInstallRoutine('import_files', 60, t('Importing files.')),
		new StartingPointInstallRoutine('install_content', 65, t('Adding pages and content.')),
		new StartingPointInstallRoutine('set_site_permissions', 80, t('Setting up site permissions.')),
		new StartingPointInstallRoutine('precache', 85, t('Prefetching information.')),
		new StartingPointInstallRoutine('finish', 95, t('Finishing.'))
		);
	}

	public function precache() {
		$c = Page::getByID(HOME_CID);
		$blocks = $c->getBlocks();
		foreach($blocks as $b) {
			$bi = $b->getInstance();
			$bi->setupAndRun('view');
		}
		$c = Page::getByPath('/dashboard/home');
		$blocks = $c->getBlocks();
		foreach($blocks as $b) {
			$bi = $b->getInstance();
			$bi->setupAndRun('view');
		}	}
	
	public function install_blocks_attributes_permissions_jobs() {
		Loader::library('content/importer');
		Page::addHomePage();
		$ci = new ContentImporter();
		$ci->importContentFile($this->getPackagePath() . '/install_blocks_attributes_permissions_jobs.xml');

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
		
		$sql = file_get_contents($installDirectory . '/install/schema.sql');
		$schema = explode("\n\n", $sql);
		
		$db = Loader::db();
		$db->ensureEncoding();
		foreach ($schema as $statement) {
			if (trim($statement) != "") { 
				$r = $db->execute($statement);
				if (!$r) { 
					throw new Exception(t('Unable to install database: %s', $db->ErrorMsg()));
				}
			}
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
		$u = User::getByUserID(USER_SUPER_ID, true);
	}
	
	public function make_directories() {
		Cache::flush();
		
		if (!is_dir(DIR_FILES_UPLOADED_THUMBNAILS)) {
			mkdir(DIR_FILES_UPLOADED_THUMBNAILS);
		}
		if (!is_dir(DIR_FILES_INCOMING)) {
			mkdir(DIR_FILES_INCOMING);
		}
		if (!is_dir(DIR_FILES_TRASH)) {
			mkdir(DIR_FILES_TRASH);
		}
		if (!is_dir(DIR_FILES_CACHE)) {
			mkdir(DIR_FILES_CACHE);
		}
		if (!is_dir(DIR_FILES_CACHE_DB)) {
			mkdir(DIR_FILES_CACHE_DB);
		}
		if (!is_dir(DIR_FILES_AVATARS)) {
			mkdir(DIR_FILES_AVATARS);
		}
	}
	
	public function finish() {
		rename(DIR_CONFIG_SITE . '/site_install.php', DIR_CONFIG_SITE . '/site.php');
		@unlink(DIR_CONFIG_SITE . '/site_install_user.php');
		@chmod(DIR_CONFIG_SITE . '/site.php', 0777);
	}
	
	public function set_site_permissions() {
		
		Loader::model('file_set');
		$fs = FileSet::getGlobal();
		$g1 = Group::getByID(GUEST_GROUP_ID);
		$g2 = Group::getByID(REGISTERED_GROUP_ID);
		$g3 = Group::getByID(ADMIN_GROUP_ID);
		
		$fs->setPermissions($g1, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE);
		$fs->setPermissions($g2, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE);
		$fs->setPermissions($g3, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL);

		Config::save('SITE', SITE);
		Config::save('SITE_APP_VERSION', APP_VERSION);
		$u = new User();
		$u->saveConfig('NEWSFLOW_LAST_VIEWED', 'FIRSTRUN');
		
		$args = array();
		$args['cInheritPermissionsFrom'] = 'OVERRIDE';
		$args['cOverrideTemplatePermissions'] = 1;
		$args['collectionRead'][] = 'gID:' . GUEST_GROUP_ID;
		$args['collectionAdmin'][] = 'gID:' . ADMIN_GROUP_ID;
		$args['collectionRead'][] = 'gID:' . ADMIN_GROUP_ID;
		$args['collectionApprove'][] = 'gID:' . ADMIN_GROUP_ID;
		$args['collectionReadVersions'][] = 'gID:' . ADMIN_GROUP_ID;
		$args['collectionWrite'][] = 'gID:' . ADMIN_GROUP_ID;
		$args['collectionDelete'][] = 'gID:' . ADMIN_GROUP_ID;
		
		$home = Page::getByID(1, "RECENT");
		$home->updatePermissions($args);
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


class StartingPointInstallRoutine {
	
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