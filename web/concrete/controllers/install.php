<?
defined('C5_EXECUTE') or die("Access Denied.");
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

define('DIR_FILES_INCOMING', DIR_FILES_UPLOADED . '/incoming');
define('DIR_FILES_UPLOADED_THUMBNAILS', DIR_FILES_UPLOADED . '/thumbnails');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_FILES_UPLOADED . '/thumbnails/level2');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_FILES_UPLOADED . '/thumbnails/level3');
define('DIR_FILES_AVATARS', DIR_FILES_UPLOADED . '/avatars');

class InstallController extends Controller {

	public $helpers = array('form', 'html');
	
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
			"DIR_FILES_INCOMING" => DIR_FILES_INCOMING,
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
	
	protected function getLocales() {
		Loader::library('3rdparty/Zend/Locale');
		$languages = Localization::getAvailableInterfaceLanguages();
		if (count($languages) > 0) { 
			array_unshift($languages, 'en_US');
		}
		$locales = array();
		foreach($languages as $lang) {
			$loc = new Zend_Locale($lang);
			$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', $lang);
		}
		return $locales;
	}
	
	public function view() {
		$locales = $this->getLocales();
		$this->set('locales', $locales);		
	}
	
	public function select_language() {
		
	}

	protected function installDB() {
		 
		// first, we install the schema
		
		$installDirectory = $this->installData['DIR_BASE_CORE'] . '/config';
		
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
	
	protected function installStarterContent() {
		Loader::model('package/starting_point');
		Loader::library('content/importer');
		$installDirectory = $this->installData['DIR_BASE_CORE'] . '/config';
		$ci = new ContentImporter();
		$ci->importContentFile($installDirectory . '/install/base/block_types.xml');
		$ci->importContentFile($installDirectory . '/install/base/attributes.xml');
		Page::addHomePage();
		$ci->importContentFile($installDirectory . '/install/base/dashboard_and_system_pages.xml');
		//$spl = Loader::startingPointPackage('blank');
		//$spl->install();
	}
	
	/** 
	 * Testing
	 */
	public function on_start() {
		if (isset($_POST['locale']) && $_POST['locale']) {
			define("ACTIVE_LOCALE", $_POST['locale']);
			$this->set('locale', $_POST['locale']);
		}
		Cache::disableCache();
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
		$this->set('remoteFileUploadTest', function_exists('iconv'));
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

	public function test_url($num1, $num2) {
		$js = Loader::helper('json');
		$num = $num1 + $num2;
		print $js->encode(array('response' => $num));
		exit;
	}

	public function configure() {
		try {

			$val = Loader::helper('validation/form');
			$val->setData($this->post());
			$val->addRequired("SITE", t("Please specify your site's name"));
			$val->addRequiredEmail("uEmail", t('Please specify a valid email address'));
			$val->addRequired("DB_DATABASE", t('You must specify a valid database name'));
			$val->addRequired("DB_SERVER", t('You must specify a valid database server'));
			
			$password = $_POST['uPassword'];
			$passwordConfirm = $_POST['uPasswordConfirm'];

			$e = Loader::helper('validation/error');
			$uh = Loader::helper('concrete/user');
			$uh->validNewPassword($password, $e);
	
			if ($password) {
				if ($password != $passwordConfirm) {
					$e->add(t('The two passwords provided do not match.'));
				}
			}
			
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

				Cache::flush();
				
				if (!is_dir($this->installData['DIR_FILES_UPLOADED_THUMBNAILS'])) {
					mkdir($this->installData['DIR_FILES_UPLOADED_THUMBNAILS']);
				}
				if (!is_dir($this->installData['DIR_FILES_INCOMING'])) {
					mkdir($this->installData['DIR_FILES_INCOMING']);
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
				
				if (isset($_POST['packages'])) {
					$this->installData['packages'] = $_POST['packages'];
				}
				
				$this->installDB();
				$this->installStarterContent();
				
				$vh = Loader::helper('validation/identifier');
				
				// copy the files
				
				// insert admin user into the user table
				$salt = ( defined('MANUAL_PASSWORD_SALT') ) ? MANUAL_PASSWORD_SALT : $vh->getString(64);
				$uPassword = $_POST['uPassword'];
				$uEmail = $_POST['uEmail'];
				
				$uPasswordEncrypted = User::encryptPassword($uPassword, $salt);
				UserInfo::addSuperUser($uPasswordEncrypted, $uEmail);

				if (defined('PERMISSIONS_MODEL') && PERMISSIONS_MODEL != 'simple') {
					$setPermissionsModel = PERMISSIONS_MODEL;
				}
				
				if (file_exists($this->installData['DIR_CONFIG_SITE'])) {	
					$this->fp = @fopen($this->installData['DIR_CONFIG_SITE'] . '/site.php', 'w+');
					if ($this->fp) {
					
												
						if (is_array($this->installData['packages'])) {
							foreach($this->installData['packages'] as $pkgHandle) {
								$p = Loader::package($pkgHandle);
								$p->install();
							}
						}
						
						// write the config file
						$configuration = "<?php\n";
						$configuration .= "define('DB_SERVER', '" . addslashes($_POST['DB_SERVER']) . "');\n";
						$configuration .= "define('DB_USERNAME', '" . addslashes($_POST['DB_USERNAME']) . "');\n";
						$configuration .= "define('DB_PASSWORD', '" . addslashes($_POST['DB_PASSWORD']) . "');\n";
						$configuration .= "define('DB_DATABASE', '" . addslashes($_POST['DB_DATABASE']) . "');\n";
						$configuration .= "define('BASE_URL', '" . $this->installData['BASE_URL'] . "');\n";
						$configuration .= "define('DIR_REL', '" . $this->installData['DIR_REL'] . "');\n";
						if (isset($setPermissionsModel)) {
							$configuration .= "define('PERMISSIONS_MODEL', '" . addslashes($setPermissionsModel) . "');\n";
						}
						if (defined('ACTIVE_LOCALE') && ACTIVE_LOCALE != '' && ACTIVE_LOCALE != 'en_US') {
							$configuration .= "define('LOCALE', '" . ACTIVE_LOCALE . "');\n";
						}
						$configuration .= "define('PASSWORD_SALT', '{$salt}');\n";
						if (is_array($_POST['SITE_CONFIG'])) {
							foreach($_POST['SITE_CONFIG'] as $key => $value) { 
								$configuration .= "define('" . $key . "', '" . $value . "');\n";
							}
						}
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
						Config::save('FULL_PAGE_CACHE_GLOBAL', 0);
						
						// login 
						define('PASSWORD_SALT', $salt);
						$u = new User($this->installData['USER_SUPER'], $uPassword);
						$this->set('message', t('Congratulations. concrete5 has been installed. You have been logged in as <b>%s</b> with the password <b>%s</b>.<br/><br/>If you wish to change this password, you may do so from the users area of the dashboard.', $this->installData['USER_SUPER'], $uPassword));
						
						
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

