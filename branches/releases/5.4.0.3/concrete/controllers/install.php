<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
if (!defined('E_DEPRECATED')) {
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
}

ini_set('display_errors', 1);
if (!ini_get('safe_mode')) {
	@set_time_limit(120);
}

date_default_timezone_set(@date_default_timezone_get());

define('ENABLE_CACHE', false);
define('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.jpg;');
if (!defined('DIR_FILES_UPLOADED')) {
	define('DIR_FILES_UPLOADED', DIR_FILES_UPLOADED_STANDARD);
}
if (!defined('DIR_FILES_TRASH')) {
	define('DIR_FILES_TRASH', DIR_FILES_TRASH_STANDARD);
}
define('DIR_FILES_UPLOADED_THUMBNAILS', DIR_FILES_UPLOADED . '/thumbnails');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_FILES_UPLOADED . '/thumbnails/level2');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_FILES_UPLOADED . '/thumbnails/level3');
define('DIR_FILES_AVATARS', DIR_FILES_UPLOADED . '/avatars');

class InstallController extends Controller {

	public $helpers = array('form', 'html');
	private $fp;
	
	// default values to be the currently defined vals
	private $installData = array(
			"DIR_BASE_CORE"=>DIR_BASE_CORE,
			"DIR_FILES_BIN_HTMLDIFF"=>DIR_FILES_BIN_HTMLDIFF,
			"DIR_BASE"=>DIR_BASE,
			"DIR_REL"=>DIR_REL,
			"BASE_URL"=>BASE_URL,
			"DIR_CONFIG_SITE" => DIR_CONFIG_SITE,
			"DIR_FILES_UPLOADED"=>DIR_FILES_UPLOADED,
			"DIR_FILES_UPLOADED_THUMBNAILS"=>DIR_FILES_UPLOADED_THUMBNAILS,
			"DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2" => DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2,
			"DIR_FILES_TRASH"=>DIR_FILES_TRASH,
			"DIR_FILES_CACHE"=>DIR_FILES_CACHE,
			"DIR_FILES_CACHE_DB"=>DIR_FILES_CACHE_DB,
			"DIR_FILES_AVATARS"=>DIR_FILES_AVATARS,
			"DIR_PACKAGES"=>DIR_PACKAGES,
			"USER_SUPER_ID"=>USER_SUPER_ID,
			"USER_SUPER"=>USER_SUPER,
			"GUEST_GROUP_ID"=>GUEST_GROUP_ID,
			"ADMIN_GROUP_ID"=>ADMIN_GROUP_ID,
			"APP_VERSION"=>APP_VERSION,
			"DEBUG_DISPLAY_ERRORS"=>DEBUG_DISPLAY_ERRORS,
			"uPassword"=>NULL
		);
	
	public function setInstallData($data) {
		// reset only the supplied vals
		foreach($data as $key=>$value) {
			$this->installData[$key] = $value;
		}
	}
	
	protected function installDB() {
		 
		
		$installDirectory = $this->installData['DIR_BASE_CORE'] . '/config';
		$file = $installDirectory . '/db.xml';
		if (!file_exists($file)) {
			throw new Exception(t('Unable to locate database import file.'));
		}
		
		$db = Loader::db();
		$db->ensureEncoding();
		$err = Package::installDB($file);		

	}
	
	protected function installDBPost() {
		// we can't seem to add this in db.xml *sigh*
		$db = Loader::db();
		$db->Execute('alter table PagePaths add index cPath (cPath(128))');
		$db->Execute('alter table CollectionVersions add index cvName (cvName(128))');
	}
	
	public function test_url($num1, $num2) {
		$js = Loader::helper('json');
		$num = $num1 + $num2;
		print $js->encode(array('response' => $num));
		exit;
	}
	
	public function on_start() {
		$this->setRequiredItems();
		$this->setOptionalItems();
	}
	
	private function setRequiredItems() {
		$this->set('imageTest', function_exists('imagecreatetruecolor'));
		$this->set('mysqlTest', function_exists('mysql_connect'));
		$this->set('xmlTest', function_exists('xml_parse') && function_exists('simplexml_load_file'));
		$this->set('fileWriteTest', $this->testFileWritePermissions());	
	}
	
	private function setOptionalItems() {
		// no longer need lucene
		//$this->set('searchTest', function_exists('iconv') && function_exists('mb_strtolower') && (@preg_match('/\pL/u', 'a') == 1));
		// no longer need built-in gettext
		//$this->set('langTest', Localization::isAvailable() && (!ini_get('safe_mode')));
		$diffExecTest = is_executable($this->installData['DIR_FILES_BIN_HTMLDIFF']);
		$diffSystem = (!ini_get('safe_mode'));
		if ($diffExecTest && $diffSystem) {
			$this->set('diffTest', true);
		} else {
			$this->set('diffTest', false);
		}
		
		if (version_compare(PHP_VERSION, '5.2.0', '>')) {
			$phpVtest = true;
		} else {
			$phpVtest = false;
		}
		$this->set('phpVtest',$phpVtest);
		
	}
	
	public function passedRequiredItems() {
		if ($this->get('imageTest') && $this->get('mysqlTest') && $this->get('fileWriteTest') && $this->get('xmlTest')) {
			return true;
		}
	}

	private function testFileWritePermissions() {
		$e = Loader::helper('validation/error');

		if (!is_writable($this->installData['DIR_CONFIG_SITE'])) {
			$e->add(t('Your configuration directory config/ does not appear to be writable by the web server.'));
		}

		if (!is_writable($this->installData['DIR_FILES_UPLOADED'])) {
			$e->add(t('Your files directory files/ does not appear to be writable by the web server.'));
		}
		
		if (!is_writable($this->installData['DIR_PACKAGES'])) {
			$e->add(t('Your packages directory packages/ does not appear to be writable by the web server.'));
		}

		$this->fileWriteErrors = $e;
		if ($this->fileWriteErrors->has()) {
			return false;
		} else {
			return true;
		}
	}
	
	public function getDBErrorMsg() {
		return t('Function mysql_connect() not found. Your system does not appear to have MySQL available within PHP.');
	}
	
	public function configure() {
		try {

			$val = Loader::helper('validation/form');
			$val->setData($this->post());
			$val->addRequired("SITE", t("Please specify your site's name"));
			$val->addRequiredEmail("uEmail", t('Please specify a valid email address'));
			$val->addRequired("DB_DATABASE", t('You must specify a valid database name'));
			$val->addRequired("DB_SERVER", t('You must specify a valid database server'));
			
			$e = Loader::helper('/validation/error');
			
			if(is_object($this->fileWriteErrors)) {
				$e = $this->fileWriteErrors;
			}
			
			if (!function_exists('mysql_connect')) {
				$e->add($this->getDBErrorMsg());
			} else {

				// attempt to connect to the database
				$db = Loader::db( $_POST['DB_SERVER'], $_POST['DB_USERNAME'], $_POST['DB_PASSWORD'], $_POST['DB_DATABASE'], true);			
				
				if ($_POST['DB_SERVER'] && $_POST['DB_DATABASE']) {
					if (!$db) {
						$e->add(t('Unable to connect to database.'));
					} else {
						
						$num = $db->GetCol("show tables");
						if (count($num) > 0) {
							$e->add(t('There are already %s tables in this database. Concrete must be installed in an empty database.', count($num)));
						}
					}
				}
			}
			
			if ($val->test() && (!$e->has())) {
				
				if (!is_dir($this->installData['DIR_FILES_UPLOADED_THUMBNAILS'])) {
					mkdir($this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
				}
				if (!is_dir($this->installData['DIR_FILES_TRASH'])) {
					mkdir($this->installData['DIR_FILES_TRASH']);
				}
				if (!is_dir($this->installData['DIR_FILES_CACHE'])) {
					mkdir($this->installData['DIR_FILES_CACHE']);
				}
				if (!is_dir($this->installData['DIR_FILES_CACHE_DB'])) {
					mkdir($this->installData['DIR_FILES_CACHE_DB']);
				}
				if (!is_dir($this->installData['DIR_FILES_AVATARS'])) {
					mkdir($this->installData['DIR_FILES_AVATARS']);
				}
				
				if (isset($_POST['uPasswordForce'])) {
					$this->installData['uPassword'] = $_POST['uPasswordForce'];
				}

				if (isset($_POST['packages'])) {
					$this->installData['packages'] = $_POST['packages'];
				}
				
				$this->installDB();
				$this->installDBPost();

				$vh = Loader::helper('validation/identifier');
				
				// insert admin user into the user table
				$salt = ( defined('MANUAL_PASSWORD_SALT') ) ? MANUAL_PASSWORD_SALT : $vh->getString(64);
				if(!isset($this->installData['uPassword'])) {
					$uPassword = rand(100000, 999999);
				} else {
					$uPassword = $this->installData['uPassword'];
				}
				$uEmail = $_POST['uEmail'];
				$uPasswordEncrypted = User::encryptPassword($uPassword, $salt);
				UserInfo::addSuperUser($uPasswordEncrypted, $uEmail);
				if (defined('PERMISSIONS_MODEL') && PERMISSIONS_MODEL != 'simple') {
					$setPermissionsModel = PERMISSIONS_MODEL;
				}
				
				if (file_exists($this->installData['DIR_CONFIG_SITE'])) {
	
					$this->fp = @fopen($this->installData['DIR_CONFIG_SITE'] . '/site.php', 'w+');
					if ($this->fp) {
					
						Cache::flush();
						
						Loader::model('single_page');
						Loader::model('dashboard/homepage');
						Loader::model('collection_types');
						Loader::model('user_attributes');
						Loader::model('collection_attributes');
						Loader::model("job");
						Loader::model("groups");
						
						// Add the home page to the system
						$home = Page::addHomePage();

						// Email
						Loader::library('mail/importer');
						MailImporter::add(array('miHandle' => 'private_message'));
						
						// create the groups our site users
						// have to add these in the right order so their IDs get set
						// starting at 1 w/autoincrement
						$g1 = Group::add(t("Guest"), t("The guest group represents unregistered visitors to your site."));
						$g2 = Group::add(t("Registered Users"), t("The registered users group represents all user accounts."));
						$g3 = Group::add(t("Administrators"), "");
						
						$cakc = AttributeKeyCategory::add('collection');
						$uakc = AttributeKeyCategory::add('user');
						$fakc = AttributeKeyCategory::add('file');
						
						// Now the default site!
						// Add our right nav page type
						$data = array();
						$data['ctHandle'] = 'right_sidebar';
						$data['ctName'] = t('Right Sidebar');
						$data['ctIcon'] = 'template3.png'; 
						$data['uID'] = $this->installData['USER_SUPER_ID'];
						$rst = CollectionType::add($data);
						
						// Add our left nav page type
						$data = array();
						$data['ctHandle'] = 'left_sidebar';
						$data['ctName'] = t('Left Sidebar');
						$data['ctIcon'] = 'template1.png';
						$data['uID'] = $this->installData['USER_SUPER_ID'];
						$dt = CollectionType::add($data);	
						
						// Add our no side nav page type
						$data = array();
						$data['ctHandle'] = 'full';
						$data['ctName'] = t('Full Width');
						$data['ctIcon'] = 'main.png'; 
						$data['uID'] = $this->installData['USER_SUPER_ID'];
						$nst = CollectionType::add($data);		
						
						// update the home page to set page type to the right sidebar one
						$data = array();
						$data['ctID'] = $rst->getCollectionTypeID();
						$home->update($data);
						
						$tt = AttributeType::add('text', t('Text'));
						$textareat = AttributeType::add('textarea', t('Text Area'));
						$boolt = AttributeType::add('boolean', t('Checkbox'));
						$dtt = AttributeType::add('date_time', t('Date/Time'));
						$ift = AttributeType::add('image_file', t('Image/File'));
						$nt = AttributeType::add('number', t('Number'));
						$rt = AttributeType::add('rating', t('Rating'));
						$st = AttributeType::add('select', t('Select'));
						$addresst = AttributeType::add('address', t('Address'));
						
						// assign collection attributes
						$cakc->associateAttributeKeyType($tt);
						$cakc->associateAttributeKeyType($textareat);
						$cakc->associateAttributeKeyType($boolt);
						$cakc->associateAttributeKeyType($dtt);
						$cakc->associateAttributeKeyType($ift);
						$cakc->associateAttributeKeyType($nt);
						$cakc->associateAttributeKeyType($rt);
						$cakc->associateAttributeKeyType($st);

						// assign user attributes
						$uakc->associateAttributeKeyType($tt);
						$uakc->associateAttributeKeyType($textareat);
						$uakc->associateAttributeKeyType($boolt);
						$uakc->associateAttributeKeyType($dtt);
						$uakc->associateAttributeKeyType($nt);
						$uakc->associateAttributeKeyType($st);
						$uakc->associateAttributeKeyType($addresst);

						// assign file attributes
						$fakc->associateAttributeKeyType($tt);
						$fakc->associateAttributeKeyType($textareat);
						$fakc->associateAttributeKeyType($boolt);
						$fakc->associateAttributeKeyType($dtt);
						$fakc->associateAttributeKeyType($nt);
						$fakc->associateAttributeKeyType($rt);
						$fakc->associateAttributeKeyType($st);
						
						// install everything into db

						// Add default page attributes
						//$cab1 = CollectionAttributeKey::add('meta_title', t('Meta Title'), true, null, 'TEXT');
						$cab1 = CollectionAttributeKey::add($tt, array('akHandle' => 'meta_title', 'akName' => t('Meta Title'), 'akIsSearchable' => true));
						$cab2 = CollectionAttributeKey::add($textareat, array('akHandle' => 'meta_description', 'akName' => t('Meta Description'), 'akIsSearchable' => true));
						$cab3 = CollectionAttributeKey::add($textareat, array('akHandle' => 'meta_keywords', 'akName' => t('Meta Keywords'), 'akIsSearchable' => true, null));
						$cab4 = CollectionAttributeKey::add($boolt, array('akHandle' => 'exclude_nav', 'akName' => t('Exclude From Nav'), 'akIsSearchable' => true));
						$cab4b = CollectionAttributeKey::add($boolt, array('akHandle' => 'exclude_page_list', 'akName' => t('Exclude From Page List'), 'akIsSearchable' => true));
						
						$cab5 = CollectionAttributeKey::add($textareat, array('akHandle' => 'header_extra_content', 'akName' => t('Header Extra Content'), 'akIsSearchable' => true));
						$cab6 = CollectionAttributeKey::add($boolt, array('akHandle' => 'exclude_search_index', 'akName' => t('Exclude From Search Index'), 'akIsSearchable' => true));
						$cab7 = CollectionAttributeKey::add($boolt, array('akHandle' => 'exclude_sitemapxml', 'akName' => t('Exclude From sitemap.xml'), 'akIsSearchable' => true, null));
						
						$dt->assignCollectionAttribute($cab1);
						$dt->assignCollectionAttribute($cab2);
						$dt->assignCollectionAttribute($cab3);
						$dt->assignCollectionAttribute($cab4); 

						$rst->assignCollectionAttribute($cab1);
						$rst->assignCollectionAttribute($cab2);
						$rst->assignCollectionAttribute($cab3);
						$rst->assignCollectionAttribute($cab4); 

						$nst->assignCollectionAttribute($cab1);
						$nst->assignCollectionAttribute($cab2);
						$nst->assignCollectionAttribute($cab3);
						$nst->assignCollectionAttribute($cab4); 

						$uakdob = UserAttributeKey::add($dtt, array('akHandle' => 'date_of_birth', 'akName' => t('Date of Birth'), 'akIsSearchable' => true, 'uakProfileEdit' => true));
						$dobcnt = $uakdob->getAttributeType()->getController();
						$dobcnt->setAttributeKey($uakdob);
						$dobcnt->setDisplayMode('text');

						UserAttributeKey::add($boolt, array('akHandle' => 'profile_private_messages_enabled', 'akName' => t('I would like to receive private messages.'), 'akIsSearchable' => true, 'uakProfileEdit' => true, 'uakRegisterEdit' => true, 'akCheckedByDefault' => true));
						UserAttributeKey::add($boolt, array('akHandle' => 'profile_private_messages_notification_enabled', 'akName' => t('Send me email notifications when I receive a private message.'), 'akIsSearchable' => true, 'uakProfileEdit' => true, 'uakRegisterEdit' => true, 'akCheckedByDefault' => true));
						
						// Add our core views
						SinglePage::add('/login');
						SinglePage::add('/register');
						SinglePage::add('/profile');
						SinglePage::add('/profile/edit');
						SinglePage::add('/members');
						SinglePage::add('/profile/avatar');
						SinglePage::add('/profile/messages');
						SinglePage::add('/profile/friends');
						SinglePage::add('/page_not_found');
						SinglePage::add('/page_forbidden');
				
						// Install our blocks
						$res = BlockType::installBlockType('content'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('html'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('autonav'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('external_form'); 	if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('form'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('page_list'); 		if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('file'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('image'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('flash_content'); 	if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('guestbook'); 		if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('slideshow'); 		if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('search'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('google_map'); 		if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('video'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('rss_displayer'); 	if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('youtube'); 			if(strlen($res)) { throw new Exception($res);}
						$res = BlockType::installBlockType('survey'); 			if(strlen($res)) { throw new Exception($res);}
						
						// Setup the default Theme
						$pl = PageTheme::add('default');
						$pl->applyToSite();
						
						// add the greensalad theme 						
						$salad = PageTheme::add('greensalad');
						
						// add the dark chocolate theme 						
						$chocolate = PageTheme::add('dark_chocolate');
						
						// Add our dashboard items and their navs
						$d0 = SinglePage::add('/dashboard');
				
						$d1 = SinglePage::add('/dashboard/sitemap');
						$d1a = SinglePage::add('/dashboard/sitemap/full');
						$d1b = SinglePage::add('/dashboard/sitemap/explore');
						$d1c = SinglePage::add('/dashboard/sitemap/search');
						$d1d = SinglePage::add('/dashboard/sitemap/access');
						$d2 = SinglePage::add('/dashboard/files');
						$d2a = SinglePage::add('/dashboard/files/search');
						$d2b = SinglePage::add('/dashboard/files/attributes');
						$d2c = SinglePage::add('/dashboard/files/sets');
						$d2d = SinglePage::add('/dashboard/files/access');						
						$d3 = SinglePage::add('/dashboard/reports');
						$d3a = SinglePage::add('/dashboard/reports/forms');
						$d3b = SinglePage::add('/dashboard/reports/surveys');
						$d3c = SinglePage::add('/dashboard/reports/logs');
						$d4 = SinglePage::add('/dashboard/users');
						$d4a = SinglePage::add('/dashboard/users/search');
						$d4b = SinglePage::add('/dashboard/users/add');
						$d4c = SinglePage::add('/dashboard/users/groups');
						$d4d = SinglePage::add('/dashboard/users/attributes');
						$d4e = SinglePage::add('/dashboard/users/registration');
						$d5 = SinglePage::add('/dashboard/scrapbook');
						
						$d7 = SinglePage::add('/dashboard/pages');
						$d71 = SinglePage::add('/dashboard/pages/themes');
						$d7a = SinglePage::add('/dashboard/pages/themes/add');
						$d7b = SinglePage::add('/dashboard/pages/themes/inspect');
						$d7c = SinglePage::add('/dashboard/pages/themes/customize');
						$d7d = SinglePage::add('/dashboard/pages/themes/marketplace');
						$d7e = SinglePage::add('/dashboard/pages/types');
						$d7f = SinglePage::add('/dashboard/pages/attributes');
						$d7g = SinglePage::add('/dashboard/pages/single');

						$d8 = SinglePage::add('/dashboard/install');
						$d9 = SinglePage::add('/dashboard/system');
						$d9a = SinglePage::add('/dashboard/system/jobs');
						$d9b = SinglePage::add('/dashboard/system/backup');
						$d9c = SinglePage::add('/dashboard/system/update');
						$d9d = SinglePage::add('/dashboard/system/notifications');
						$d10 = SinglePage::add('/dashboard/settings');
						$d11 = SinglePage::add('/dashboard/settings/mail');
						$d12 = SinglePage::add('/dashboard/settings/marketplace');
						
						
						// add home page
						$dl1 = SinglePage::add('/download_file');
						$dl1->update(array('cName' => t('Download File')));
						
						$d1->update(array('cName'=>t('Sitemap'), 'cDescription'=>t('Whole world at a glance.')));
						$d1a->update(array('cName'=>t('Full Sitemap')));
						$d1b->update(array('cName'=>t('Folder View')));
						$d1c->update(array('cName'=>t('Page Search')));

						$d2->update(array('cName'=>t('File Manager'), 'cDescription'=>t('All documents and images.')));
						$d2a->update(array('cName'=>t('Search')));
						$d2b->update(array('cName'=>t('Attributes')));
						$d2c->update(array('cName'=>t('Sets')));
						$d2d->update(array('cName'=>t('Access')));						
						$d3->update(array('cName'=>t('Reports'), 'cDescription'=>t('Get data from forms and logs.')));
						$d3a->update(array('cName'=>t('Form Results'), 'cDescription'=>t('Get submission data.')));
						$d3b->update(array('cName'=>t('Surveys')));
						$d3c->update(array('cName'=>t('Logs')));
						$d4->update(array('cName'=>t('Users and Groups'), 'cDescription'=>t('Add and manage people.')));
						$d4a->update(array('cName'=>t('Find Users')));
						$d4b->update(array('cName'=>t('Add User')));
						$d4c->update(array('cName'=>t('Groups')));
						$d4d->update(array('cName'=>t('User Attributes')));
						$d4e->update(array('cName'=>t('Login & Registration')));
						$d5->update(array('cName'=>t('Scrapbook'), 'cDescription'=>t('Share content across your site.')));	
						$d7->update(array('cName'=>t('Pages and Themes'), 'cDescription'=>t('Reskin your site.')));	
						$d71->update(array('cName'=>t('Themes'), 'cDescription'=>t('Reskin your site.')));	
						$d7e->update(array('cName'=>t('Page Types'), 'cDescription'=>t('What goes in your site.')));	
						$d7g->update(array('cName'=>t('Single Pages')));	

						$d8->update(array('cName'=>t('Add Functionality'), 'cDescription'=>t('Install addons & themes.')));
						$d9->update(array('cName'=>t('System & Maintenance'), 'cDescription'=>t('Backup, cleanup and update.')));
						$d9b->update(array('cName'=>t('Backup & Restore')));	
						$d10->update(array('cName'=>t('Sitewide Settings'), 'cDescription'=>t('Secure and setup your site.')));

						$d11->update(array('cName'=>t('Email'), 'cDescription'=>t('Enable post via email and other settings.')));
				
						// dashboard homepage
						$dh2 = new DashboardHomepageView();
						$dh2->add('activity', t('Site Activity'));
						$dh2->add('reports', t('Statistics'));
						$dh2->add('help', t('Help'));
						$dh2->add('news', t('Latest News'));
						$dh2->add('notes', t('Notes'));
						
						// setup header autonav block we're going to add
						$data = array();
						$data['orderBy'] = 'display_asc';
						$data['displayPages'] = 'top';
						$data['displaySubPages'] = 'none';		
						$data['uID'] = $this->installData['USER_SUPER_ID'];
						$autonav = BlockType::getByHandle('autonav');

						// add autonav block to left sidebar page type
						$detailTemplate = $dt->getMasterTemplate();
						$b1 = $detailTemplate->addBlock($autonav, 'Header Nav', $data);
						$b1->setCustomTemplate('header_menu.php');
						
						// Add an autonav block to right sidebar page type
						$rightNavTemplate = $rst->getMasterTemplate();
						$b2 = $rightNavTemplate->addBlock($autonav, 'Header Nav', $data);
						$b2->setCustomTemplate('header_menu.php');

						// Add an autonav block to full width header
						$fullWidthTemplate = $nst->getMasterTemplate();
						$b3 = $fullWidthTemplate->addBlock($autonav, 'Header Nav', $data);
						$b3->setCustomTemplate('header_menu.php');
						
						// Add an autonav block to Every detail page sidebar
						$data = array();
						$data['orderBy'] = 'display_asc';
						$data['displayPages'] = 'second_level';
						$data['displaySubPages'] = 'relevant';
						$data['displaySubPageLevels'] = 'none';
						$data['uID'] = $this->installData['USER_SUPER_ID'];
						$b2 = $detailTemplate->addBlock($autonav, 'Sidebar', $data);
						
						// Add an autonav block to Every detail page sidebar
						$b2 = $rightNavTemplate->addBlock($autonav, 'Sidebar', $data);
						
						// alias header nav to the home page
						$b1->alias($home);

						Loader::model('file_set');
						$fs = FileSet::getGlobal();
						$fs->setPermissions($g1, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE);
						$fs->setPermissions($g2, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE, FilePermissions::PTYPE_NONE);
						$fs->setPermissions($g3, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL, FilePermissions::PTYPE_ALL);
						
						$tp0 = TaskPermission::addTask('access_task_permissions', t('Change Task Permissions'), false);
						$tp1 = TaskPermission::addTask('access_sitemap', t('Access Sitemap and Page Search'), false);
						$tp2 = TaskPermission::addTask('access_user_search', t('Access User Search'), false);
						$tp3 = TaskPermission::addTask('access_group_search', t('Access Group Search'), false);
						$tp4 = TaskPermission::addTask('access_page_defaults', t('Change Content on Page Type Default Pages'), false);
						$tp5 = TaskPermission::addTask('backup', t('Perform Full Database Backups'), false);
						$tp6 = TaskPermission::addTask('sudo', t('Sign in as User'), false);
						$tp7 = TaskPermission::addTask('uninstall_packages', t('Uninstall Packages'), false);
						
						$tp1->addAccess($g3);
						$tp2->addAccess($g3);
						$tp3->addAccess($g3);
						$tp5->addAccess($g3);
						
						/* install default content */	
						if ($_POST['INSTALL_SAMPLE_CONTENT']) {
							// Add Some Imagery
							Loader::library("file/importer");
							
							$fi = new FileImporter();
							$image1 = $fi->import($pl->getThemeDirectory() . '/images/inneroptics_dot_net_aspens.jpg');
							$image2 = $fi->import($pl->getThemeDirectory() . '/images/inneroptics_dot_net_canyonlands.jpg');
							$image3 = $fi->import($pl->getThemeDirectory() . '/images/inneroptics_dot_net_new_zealand_sheep.jpg');
							$image4 = $fi->import($pl->getThemeDirectory() . '/images/inneroptics_dot_net_starfish.jpg');
							$image5 = $fi->import($pl->getThemeDirectory() . '/images/inneroptics_dot_net_portland.jpg');
							
							$image1->getFile()->setUserID($this->installData['USER_SUPER_ID']);
							$image2->getFile()->setUserID($this->installData['USER_SUPER_ID']);
							$image3->getFile()->setUserID($this->installData['USER_SUPER_ID']);
							$image4->getFile()->setUserID($this->installData['USER_SUPER_ID']);
							$image5->getFile()->setUserID($this->installData['USER_SUPER_ID']);
							
							// this is an irritating hack.
							if(DIR_FILES_UPLOADED != $this->installData['DIR_FILES_UPLOADED']) { // if we're calling install from another c5 install - move the file to the new install
								$cfhi = Loader::helper('concrete/file');
								$fsrc1 = $cfhi->mapSystemPath($image1->getPrefix(), $image1->getFileName(), true, $this->installData['DIR_FILES_UPLOADED']);
								$fsrc2 = $cfhi->mapSystemPath($image2->getPrefix(), $image2->getFileName(), true, $this->installData['DIR_FILES_UPLOADED']);
								$fsrc3 = $cfhi->mapSystemPath($image3->getPrefix(), $image3->getFileName(), true, $this->installData['DIR_FILES_UPLOADED']);
								$fsrc4 = $cfhi->mapSystemPath($image4->getPrefix(), $image4->getFileName(), true, $this->installData['DIR_FILES_UPLOADED']);
								$fsrc5 = $cfhi->mapSystemPath($image5->getPrefix(), $image5->getFileName(), true, $this->installData['DIR_FILES_UPLOADED']);
								$fsrc6 = $cfhi->mapSystemPath($image1->getPrefix(), $image1->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
								$fsrc7 = $cfhi->mapSystemPath($image2->getPrefix(), $image2->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
								$fsrc8 = $cfhi->mapSystemPath($image3->getPrefix(), $image3->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
								$fsrc9 = $cfhi->mapSystemPath($image4->getPrefix(), $image4->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
								$fsrc10 = $cfhi->mapSystemPath($image5->getPrefix(), $image5->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
								$fsrc11 = $cfhi->mapSystemPath($image1->getPrefix(), $image1->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2']);
								$fsrc12 = $cfhi->mapSystemPath($image2->getPrefix(), $image2->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2']);
								$fsrc13 = $cfhi->mapSystemPath($image3->getPrefix(), $image3->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2']);
								$fsrc14 = $cfhi->mapSystemPath($image4->getPrefix(), $image4->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2']);
								$fsrc15 = $cfhi->mapSystemPath($image5->getPrefix(), $image5->getFileName(), true, $this->installData['DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2']);
								
								rename($image1->getPath(), $fsrc1);
								rename($image2->getPath(), $fsrc2);
								rename($image3->getPath(), $fsrc3);
								rename($image4->getPath(), $fsrc4);
								rename($image5->getPath(), $fsrc5);
								rename($image1->getThumbnailPath(1), $fsrc6);
								rename($image2->getThumbnailPath(1), $fsrc7);
								rename($image3->getThumbnailPath(1), $fsrc8);
								rename($image4->getThumbnailPath(1), $fsrc9);
								rename($image5->getThumbnailPath(1), $fsrc10);
								rename($image1->getThumbnailPath(2), $fsrc11);
								rename($image2->getThumbnailPath(2), $fsrc12);
								rename($image3->getThumbnailPath(2), $fsrc13);
								rename($image4->getThumbnailPath(2), $fsrc14);
								rename($image5->getThumbnailPath(2), $fsrc15);
							}
													
							// Assign this imagery to the various pages.
							$btImage = BlockType::getByHandle('image');
							$data = array();
							$data['fID'] = $image1->getFileID();
							$data['altText'] = t('Home Header Image');
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$home->addBlock($btImage, 'Header', $data);
							
							// Assign imagery to left sidebar page
							$data['fID'] = $image2->getFileID();
							$data['altText'] = t('Left Sidebar Page Type Image');
							$b1 = $detailTemplate->addBlock($btImage, 'Header', $data);
							
							// Assign imagery to right sidebar page
							$data['fID'] = $image3->getFileID();
							$data['altText'] = t('Right Sidebar Page Type Image');
							$b2 = $rightNavTemplate->addBlock($btImage, 'Header', $data);
							
							// Assign imagery to full width page
							$data['fID'] = $image3->getFileID();
							$data['altText'] = t('Full Width Page Type Image');
							$b3 = $fullWidthTemplate->addBlock($btImage, 'Header', $data);
								
							// Add global scrapbook area
							$scrapbookHelper = Loader::helper('concrete/scrapbook');
							$scrapbookPage=$scrapbookHelper->getGlobalScrapbookPage(); 
							$globalScrapbookName = t('Global Scrapbook');
							$global_a = Area::get($scrapbookPage, $globalScrapbookName);						
							if (!is_object($global_a)) 
								$global_a = Area::getOrCreate( $scrapbookPage, $globalScrapbookName); 						
							
							// Add global scrapbook site name block	 															
							$bt = BlockType::getByHandle('content');
							$data = array();
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$data['content'] = $_POST['SITE'];					
							$b = $scrapbookPage->addBlock($bt, $globalScrapbookName, $data);
							$b->updateBlockName( 'My_Site_Name', 1 );
							
							
							// Add Content to Home page
							$bt = BlockType::getByHandle('content');
							$data = array();
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$data['content'] = t('<h1>Welcome to Concrete.</h1><p>Learn how to:</p><ul><li><a title="web editing with concrete5" href="http://www.concrete5.org/help/editing/login-incontext-editing/" target="_blank">Edit</a> this page.</li><li>Add a <a title="add pages in concrete5" href="http://www.concrete5.org/help/editing/add-a-page/" target="_blank">new page</a>.</li><li>Add some basic functionality, like <a title="os cms concrete5" href="http://www.concrete5.org/help/editing/add_a_form/" target="_blank">a Form</a>.</li><li><a title="add-on marketplace for concrete5" href="http://www.concrete5.org/help/editing/installing_a_package/" target="_blank">Finding &amp; adding</a> more functionality and themes. </li></ul><p>We\'ve taken the liberty to build out the rest of this like a typical small organization. Wander around and put these pages in edit mode to see how we did it.</p>');
							$home->addBlock($bt, "Main", $data);
							
							// add youtube video
							$ytBT = BlockType::getByHandle('youtube');
							$ytData['videoURL'] = 'http://www.youtube.com/watch?v=oYSOFTNLbKY';
							$ytData['title'] = t('Basic Editing');
							$home->addBlock($ytBT, "Main", $ytData);
							
							//add more content
							$bt = BlockType::getByHandle('content');
							$data = array();
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$data['content'] = t('<h3>Learn More</h3><p>Visit concrete5.org to learn more from the <a title="open source content management system" href="http://concrete5.org/community" target="_blank">community</a> and the <a title="CMS concrete5" href="http://concrete5.org/help" target="_blank">help</a> section.</p>');
							$home->addBlock($bt, "Main", $data);
							
							//sidebar content
							$bt = BlockType::getByHandle('content');
							$data = array();
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$data['content'] = t('<h2>Sidebar</h2><p>Everything about Concrete is completely customizable through the CMS. This is a separate area from the main content on the homepage. You can <a title="blocks on concrete5" href="http://www.concrete5.org/help/editing/arrange_blocks_on_a_page/" target="_blank">drag and drop blocks</a> like this around your layout.</p>');
							$home->addBlock($bt, "Sidebar", $data);
							
							//header content
							// remove image block from header
							$blocks = $home->getBlocks('Header');
							if (is_object($blocks[0])) {
								$blocks[0]->deleteBlock();
							}
							
							$jsBT = BlockType::getByHandle('slideshow');
							$jsData['playback'] = 'ORDER';
							$jsData['imgBIDs'] = array(
								$image1->getFileID(),
								$image4->getFileID(),
								$image3->getFileID()
							);
							
							$jsData['imgFIDs'] = array(
								$image1->getFileID(),
								$image4->getFileID(),
								$image3->getFileID()
							);
							$jsData['type'] = 'CUSTOM';
							$jsData['duration'] = array(5, 5, 5);
							$jsData['imgHeight'] = array(192, 192, 192, 192);
							$jsData['fadeDuration'] = array(2, 2, 2);
							$home->addBlock($jsBT, "Header", $jsData);
							//end home
							
							//about page
							$data = array();
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$data['name'] = t('About');
							$aboutPage = $home->add($dt, $data);
							
							//content
							$bt = BlockType::getByHandle('content');
							$data['content']  = t('<h1>Sed ut perspiciatis unde omnis iste natus error (H1)</h1><p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>');
							$aboutPage->addBlock($bt, "Main", $data);
							
							$bt = BlockType::getByHandle('content');
							$data['content']  = t('<h2>Contact Us:</h2>');
							$aboutPage->addBlock($bt, "Main", $data);
							
							//contact form
							$bt = BlockType::getByHandle('form');	
							$data['qsID']=1;
							$data['surveyName'] = t('About');
							$data['notifyMeOnSubmission'] = 0;
							$data['thankyouMsg'] = t('Thanks!');
							$data['questions'][] = array( 'qsID'=>$data['qsID'], 'question'=>t('Name:'), 'inputType'=>'field', 'options'=>'', 'position'=>1, 'required' => 1 );
							$data['questions'][] = array( 'qsID'=>$data['qsID'], 'question'=>t('Phone:'), 'inputType'=>'field', 'options'=>'', 'position'=>2 );
							$data['questions'][] = array( 'qsID'=>$data['qsID'], 'question'=>t('eMail:'), 'inputType'=>'field', 'options'=>'', 'position'=>3 );
							$data['questions'][] = array( 'qsID'=>$data['qsID'], 'question'=>t('Comments:'), 'inputType'=>'text', 'options'=>'', 'position'=>4, 'width' => 20, 'height' => 10 );
							$aboutPage->addBlock($bt, "Main", $data);	
							
							//header content
							// remove image block from header
							$blocks = $aboutPage->getBlocks('Header');
							if (is_object($blocks[0])) {
								$blocks[0]->deleteBlock();
							}
							
							$jsBT = BlockType::getByHandle('slideshow');
							$jsData['playback'] = 'ORDER';
							$jsData['imgBIDs'] = array(
								$image1->getFileID(),
								$image4->getFileID(),
								$image3->getFileID(),
							);
							
							$jsData['imgFIDs'] = array(
								$image1->getFileID(),
								$image4->getFileID(),
								$image3->getFileID(),
							);
							$jsData['type'] = 'CUSTOM';
							$jsData['duration'] = array(5, 5, 5);
							$jsData['imgHeight'] = array(192, 192, 192, 192);
							$jsData['fadeDuration'] = array(2, 2, 2);
							$aboutPage->addBlock($jsBT, "Header", $jsData);
							//about page done

							
								//press room
								$data = array();
								$data['uID'] = $this->installData['USER_SUPER_ID'];
								$data['name'] = t('Press Room');
								$pressRoom = $aboutPage->add($dt, $data);
								
								//content
								$bt = BlockType::getByHandle('content');
								$data['content']  = t('<h1>Welcome</h1><p>This a great example of how flexible page types can be. We added a page type of "press release" so we could assign some custom attributes to it and make the very PR-ish formatted custom template for the page list block below.</p><h2>Press Releases:</h2>');
								$pressRoom->addBlock($bt, "Main", $data);
								
								$pageListBT = BlockType::getByHandle('page_list');
								$plData['cParentID'] = $pressRoom->getCollectionID();
								$plData['orderBy'] = 'display_asc';
								$plData['num'] = 99;
								$plData['cThis'] = 1;
								$b1 = $pressRoom->addBlock($pageListBT, "Main", $plData);
								$b1->setCustomTemplate('custom.php');
								//end press room
								
									//press release
									$pr_date = CollectionAttributeKey::add('DATE', array('akHandle' => 'Release_Date', 'akName' => t('Release Date'), 'akIsSearchable' => true,'akDateDisplayMode'=>'date_time'));
									$pr_type = CollectionAttributeKey::add('SELECT', array('akHandle' => 'Press_Release_Type', 'akName' => t('Press Release Type'), 'akIsSearchable' => true));
									$opt = new SelectAttributeTypeOption(0, 'Press Release', 1);
									$opt = $opt->saveOrCreate($pr_type);
									$opt = new SelectAttributeTypeOption(0, 'News Item', 2);
									$opt = $opt->saveOrCreate($pr_type);
									$opt = new SelectAttributeTypeOption(0, 'Speaking/Event', 3);
									$opt = $opt->saveOrCreate($pr_type);
									
									$data = array();
									$data['ctHandle'] = 'Press Release';
									$data['ctName'] = t('Press Release');
									$data['ctIcon'] = 'template3.png'; 
									$data['uID'] = $this->installData['USER_SUPER_ID'];
									$pr_collection_type = CollectionType::add($data);
									
									$pr_collection_type->assignCollectionAttribute($pr_date);
									$pr_collection_type->assignCollectionAttribute($pr_type);
									
									$pr_collection_type->assignCollectionAttribute($cab1);
									$pr_collection_type->assignCollectionAttribute($cab2);
									$pr_collection_type->assignCollectionAttribute($cab3);
									$pr_collection_type->assignCollectionAttribute($cab4);
									
									$pr_template = $pr_collection_type->getMasterTemplate();
									$data = array();
									$data['orderBy'] = 'display_asc';
									$data['displayPages'] = 'top';
									$data['displaySubPages'] = 'none';		
									$data['uID'] = $this->installData['USER_SUPER_ID'];
									$autonav = BlockType::getByHandle('autonav');
									$b = $pr_template->addBlock($autonav, 'Header Nav', $data);
									$b->setCustomTemplate('header_menu.php');
									
									$data = array();
									$data['orderBy'] = 'display_asc';
									$data['displayPages'] = 'second_level';
									$data['displaySubPages'] = 'relevant';
									$data['displaySubPageLevels'] = 'enough_plus1';
									$data['uID'] = $this->installData['USER_SUPER_ID'];
									$b = $pr_template->addBlock($autonav, 'Sidebar', $data);
									
									$data['fID'] = $image5->getFileID();
									$data['altText'] = t('Header Image');
									$b2 = $pr_template->addBlock($btImage, 'Header', $data);
									
									$data = array();
									$data['uID'] = $this->installData['USER_SUPER_ID'];
									$data['name'] = t('Launch our new site!');
									$data['cDescription'] = t('Neeto speedo! We just rebuilt our site in record time and now we can easily change EVERYTHING.');
									
									$pressRelease = $pressRoom->add($pr_collection_type, $data);
									$pressRelease->setAttribute('Release_Date',date("Y-m-d H:i"));
									$pressRelease->setAttribute('Press_Release_Type','Press Release');
									
									$bt = BlockType::getByHandle('content');
									$data['content']  = t('<p>Vestibulum a tristique tellus. Morbi nunc orci, ornare sit amet accumsan nec, sagittis eu nisi. Sed elementum fringilla ipsum a interdum. Nulla egestas turpis at dui interdum faucibus. Proin consectetur nibh eros, eget sodales felis. Nam volutpat bibendum augue ut lacinia. Nam volutpat fringilla odio, vitae feugiat dui sagittis sed. Aenean interdum accumsan luctus. Suspendisse congue sagittis tortor ut porta. Etiam justo augue, auctor posuere iaculis ac, aliquet vitae purus. In malesuada, ipsum non vulputate semper, felis purus condimentum augue, a mollis metus nisi a diam. Nulla eu scelerisque lacus. Morbi congue massa vitae nulla auctor suscipit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut mauris quis nunc rhoncus lobortis. Aliquam sit amet dui et felis sodales volutpat nec eget nisl.</p><p>Nulla elit dui, pharetra eget elementum eu, varius sed nisi. Morbi semper interdum nisl, eget rutrum ante venenatis nec. Vivamus dignissim, justo quis semper ultricies, quam elit elementum eros, rutrum volutpat urna nulla quis dui. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed et orci ligula. In est dui, porta at posuere a, ullamcorper ut nisi. Maecenas nibh eros, suscipit et pulvinar non, tempus varius dui. Quisque nec tortor sed erat rhoncus laoreet. Sed pulvinar est eu quam adipiscing tincidunt. Donec auctor arcu et ante venenatis sagittis.</p><p>&nbsp;</p>');		
									$pressRelease->addBlock($bt, "Main", $data);
									
									$bt = BlockType::getByHandle('content');
									$data['content']  = t('<h2>About Us:<br /></h2><p>This is a content block that is part of the page\'s defaults so it will show up every time you make a new press release. If <a title="building with concrete5" href="http://www.concrete5.org/help/editing/scrapbook_defaults/" target="_blank">change it through defaults</a>, you update it everywhere!</p>');
									$pressRelease->addBlock($bt, "Main", $data);
									
									//end press release
								
								//guestbook
								// add guestbook page
								$data['name'] = t('Guestbook');
								$data['cHandle'] = 'guestbook';
								$guestbookPage = $aboutPage->add($dt, $data);
								
								$bt = BlockType::getByHandle('content');
								$data['content']  = t('<h1>We\'re happy to see you here.<br /></h1><p>Let us know you\'ve been here by signing our guestbook.</p>');
								$guestbookPage->addBlock($bt, "Main", $data);
								
								// add guestbook block
								$gbBT = BlockType::getByHandle('guestbook');
								$gbData['requireApproval'] = 0;
								$gbData['title'] = t('Comments');
								$gbData['displayGuestBookForm'] = 1;
								$gbData['authenticationRequired'] = 0;
								$gbData['displayCaptcha'] = 1;
								$guestbookPage->addBlock($gbBT, "Main", $gbData);
								
								// add survey
								$surveyBT = BlockType::getByHandle('survey');
								$surveyData['title'] = t('Do you like what you see?');
								$surveyData['question'] = t('Do you like what you see?');
								$surveyData['pollOption'] = array(t("Yes"), t("Kinda"), t("Not Really"));
								$guestbookPage->addBlock($surveyBT , "Sidebar", $surveyData);
								//end guestbook
							
							//search
							$data = array();
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$data['name'] = t('Search');
							$searchPage = $home->add($dt, $data);
							
							$bt = BlockType::getByHandle('content');
							$data['content']  = t('<h1>Sitemap</h1>');
							$searchPage->addBlock($bt, "Main", $data);
							
							$data = array();
							$data['orderBy'] = 'display_asc';
							$data['displayPages'] = 'top';
							$data['displaySubPages'] = 'all';	
							$data['displaySubPageLevels'] = 'all';
							$data['uID'] = $this->installData['USER_SUPER_ID'];
							$autonav = BlockType::getByHandle('autonav');
							$searchPage->addBlock($autonav, "Main", $data);
							
							$blocks = $searchPage->getBlocks('Sidebar');
							if (is_object($blocks[0])) {
								$blocks[0]->deleteBlock();
							}
							
							
							// add results	
								$data = array();
								$data['uID'] = $this->installData['USER_SUPER_ID'];
								$data['name'] = t('Search Results');
								$data['cHandle'] = 'search-results';
								$searchResults = $searchPage->add($dt, $data);
								
								$resultsUrl = $searchResults->getCollectionPath();
								
								$searchResults->setAttribute('exclude_nav',1);
								
								
								$blocks = $searchResults->getBlocks('Sidebar');
								if (is_object($blocks[0])) {
									$blocks[0]->deleteBlock();
								}
								// add search block to example 3 page
								$searchBT = BlockType::getByHandle('search');
								$searchData = array();
								$searchData['title'] = t('Search');
								$searchData['buttonText'] = t('Go');
								$searchResults->addBlock($searchBT, "Main", $searchData);
								
							$searchBT = BlockType::getByHandle('search');
							$searchData['title'] = t('Search Your Site');
							$searchData['buttonText'] = t('Search');
							$searchData['externalTarget'] = 1;
							$searchData['resultsURL'] = '/search/search-results';
							$searchPage->addBlock($searchBT, "Sidebar", $searchData);
							//end search
						}
						
						/* set it so anyone can read the site */
						$args = array();
						$args['cInheritPermissionsFrom'] = 'OVERRIDE';
						$args['cOverrideTemplatePermissions'] = 1;
						$args['collectionRead'][] = 'gID:' . $this->installData['GUEST_GROUP_ID'];
						$args['collectionAdmin'][] = 'gID:' . $this->installData['ADMIN_GROUP_ID'];
						$args['collectionRead'][] = 'gID:' . $this->installData['ADMIN_GROUP_ID'];
						$args['collectionApprove'][] = 'gID:' . $this->installData['ADMIN_GROUP_ID'];
						$args['collectionReadVersions'][] = 'gID:' . $this->installData['ADMIN_GROUP_ID'];
						$args['collectionWrite'][] = 'gID:' . $this->installData['ADMIN_GROUP_ID'];
						$args['collectionDelete'][] = 'gID:' . $this->installData['ADMIN_GROUP_ID'];
						
						$home = Page::getByID(1, "RECENT");
						$home->updatePermissions($args);
					
						// Install & Run Jobs  
						Job::installByHandle('index_search');
						Job::installByHandle('generate_sitemap');
						Job::installByHandle('process_email');
						Job::runAllJobs();
						
						if (is_array($this->installData['packages'])) {
							foreach($this->installData['packages'] as $pkgHandle) {
								$p = Loader::package($pkgHandle);
								$p->install();
							}
						}
						
						// write the config file
						$configuration = "<?php \n";
						$configuration .= "define('DB_SERVER', '" . addslashes($_POST['DB_SERVER']) . "');\n";
						$configuration .= "define('DB_USERNAME', '" . addslashes($_POST['DB_USERNAME']) . "');\n";
						$configuration .= "define('DB_PASSWORD', '" . addslashes($_POST['DB_PASSWORD']) . "');\n";
						$configuration .= "define('DB_DATABASE', '" . addslashes($_POST['DB_DATABASE']) . "');\n";
						$configuration .= "define('BASE_URL', '" . $this->installData['BASE_URL'] . "');\n";
						$configuration .= "define('DIR_REL', '" . $this->installData['DIR_REL'] . "');\n";
						if (isset($setPermissionsModel)) {
							$configuration .= "define('PERMISSIONS_MODEL', '" . addslashes($setPermissionsModel) . "');\n";
						}
						$configuration .= "define('PASSWORD_SALT', '{$salt}');\n";
						if (is_array($_POST['SITE_CONFIG'])) {
							foreach($_POST['SITE_CONFIG'] as $key => $value) { 
								$configuration .= "define('" . $key . "', '" . $value . "');\n";
							}
						}
						$configuration .= "?" . ">";
						$res = fwrite($this->fp, $configuration);
						fclose($this->fp);
						chmod($this->installData['DIR_CONFIG_SITE'] . '/site.php', 0777);
						
						// save some options into the database
						Config::save('SITE', $_POST['SITE']);
						// add the current app version as our site's app version
						Config::save('SITE_APP_VERSION', $this->installData['APP_VERSION']);
						Config::save('SITE_DEBUG_LEVEL', $this->installData['DEBUG_DISPLAY_ERRORS']);
						Config::save('ENABLE_LOG_EMAILS', 1);
						Config::save('ENABLE_LOG_ERRORS', 1);
						
						// login 
						define('PASSWORD_SALT', $salt);
						$u = new User($this->installData['USER_SUPER'], $uPassword);
						$this->set('message', t('Congratulations. Concrete has been installed. You have been logged in as <b>%s</b> with the password <b>%s</b>.<br/><br/>If you wish to change this password, you may do so from the users area of the dashboard.', $this->installData['USER_SUPER'], $uPassword));
						
						
					} else {
						throw new Exception(t('Unable to open config/site.php for writing.'));
					}
				
	
				} else {
					throw new Exception(t('Unable to locate config directory.'));
				}
			
			} else {
				if ($e->has()) {
					$this->set('error', $e);
				} else {
					$this->set('error', $val->getError());
				}
			}
			
		} catch (Exception $e) {
			// remove site.php so that we can try again ?
			if (is_resource($this->fp)) {
				fclose($this->fp);
			}
			if (file_exists($this->installData['DIR_CONFIG_SITE'] . '/site.php')) {
				unlink($this->installData['DIR_CONFIG_SITE'] . '/site.php');
			}
			$this->set('error', $e);
		}
	}

	
}

?>
