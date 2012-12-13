<?
defined('C5_EXECUTE') or die("Access Denied.");
ini_set('display_errors', 1);
if (!ini_get('safe_mode')) {
	@set_time_limit(150);
}

date_default_timezone_set(@date_default_timezone_get());

define('ENABLE_CACHE', false);
define('UPLOAD_FILE_EXTENSIONS_ALLOWED','*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.xlsx;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.avi;*.m4v;*.mp4;*.mp3;*.qt;*.ppt;*.pptx;*.kml;*.xml');
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

class Concrete5_Controller_Install extends Controller {

	public $helpers = array('form', 'html');
	
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
		$this->testAndRunInstall();		
	}
	
	public function setup() {
	
	}
	
	public function select_language() {
		
	}

	/** 
	 * Testing
	 */
	public function on_start() {
		if (isset($_POST['locale']) && $_POST['locale']) {
			define("ACTIVE_LOCALE", $_POST['locale']);
			$this->set('locale', $_POST['locale']);
		}
		require(DIR_BASE_CORE . '/config/file_types.php');
		Cache::disableCache();
		$this->setRequiredItems();
		$this->setOptionalItems();
		Loader::model('package/starting_point');

		if (file_exists(DIR_CONFIG_SITE . '/site.php')) {
			throw new Exception(t('concrete5 is already installed.'));
		}		
		if (!isset($_COOKIE['CONCRETE5_INSTALL_TEST'])) {
			setcookie('CONCRETE5_INSTALL_TEST', '1', 0, DIR_REL . '/');
		}
	}
	
	protected function testAndRunInstall() {
		if (file_exists(DIR_CONFIG_SITE . '/site_install_user.php')) {
			require(DIR_CONFIG_SITE . '/site_install.php');
			@include(DIR_CONFIG_SITE . '/site_install_user.php');
			if (defined('ACTIVE_LOCALE') && Localization::activeLocale() !== ACTIVE_LOCALE) {
				Localization::changeLocale(ACTIVE_LOCALE);
			}
			$e = Loader::helper('validation/error');
			$e = $this->validateDatabase($e);
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
				$this->addHeaderItem(Loader::helper('html')->javascript('jquery.ui.js'));
				if (defined('INSTALL_STARTING_POINT') && INSTALL_STARTING_POINT) { 
					$spl = Loader::startingPointPackage(INSTALL_STARTING_POINT);
				} else {
					$spl = Loader::startingPointPackage('standard');
				}
				$this->set('installPackage', $spl->getPackageHandle());
				$this->set('installRoutines', $spl->getInstallRoutines());
				$this->set('successMessage', t('Congratulations. concrete5 has been installed. You have been logged in as <b>%s</b> with the password you chose. If you wish to change this password, you may do so from the users area of the dashboard.', USER_SUPER, $uPassword));
			}
		}
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
		$phpVmin = '5.2.4';
		if (version_compare(PHP_VERSION, $phpVmin, '>=')) {
			$phpVtest = true;
		} else {
			$phpVtest = false;
		}
		$this->set('phpVmin',$phpVmin);
		$this->set('phpVtest',$phpVtest);
		
	}
	
	public function passedRequiredItems() {
		if ($this->get('imageTest') && $this->get('mysqlTest') && $this->get('fileWriteTest') && $this->get('xmlTest')) {
			return true;
		}
	}

	private function testFileWritePermissions() {
		$e = Loader::helper('validation/error');

		if (!is_writable(DIR_CONFIG_SITE)) {
			$e->add(t('Your configuration directory config/ does not appear to be writable by the web server.'));
		}

		if (!is_writable(DIR_FILES_UPLOADED)) {
			$e->add(t('Your files directory files/ does not appear to be writable by the web server.'));
		}
		
		if (!is_writable(DIR_PACKAGES)) {
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
	
	public function run_routine($pkgHandle, $routine) {
		$spl = Loader::startingPointPackage($pkgHandle);
		require(DIR_CONFIG_SITE . '/site_install.php');
		@include(DIR_CONFIG_SITE . '/site_install_user.php');
		
		$jsx = Loader::helper('json');
		$js = new stdClass;
		
		try {
			call_user_func(array($spl, $routine));
			$js->error = false;
		} catch(Exception $e) {
			$js->error = true;
			$js->message = $e->getMessage();
			$this->reset();
		}
		print $jsx->encode($js);
		exit;
	}
	
	protected function validateSampleContent($e) {
		$pkg = Loader::startingPointPackage($this->post('SAMPLE_CONTENT'));
		if (!is_object($pkg)) {
			$e->add(t("You must select a valid sample content starting point."));
		}
		return $e;
	}
	
	protected function validateDatabase($e) {
		if (!function_exists('mysql_connect')) {
			$e->add($this->getDBErrorMsg());
		} else {

			// attempt to connect to the database
			if (defined('DB_SERVER')) {
				$db = Loader::db($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, true);
				$DB_SERVER = DB_SERVER;
				$DB_DATABASE = DB_DATABASE;
			} else {
				$db = Loader::db( $_POST['DB_SERVER'], $_POST['DB_USERNAME'], $_POST['DB_PASSWORD'], $_POST['DB_DATABASE'], true);			
				$DB_SERVER = $_POST['DB_SERVER'];
				$DB_DATABASE = $_POST['DB_DATABASE'];
			}
			
			if ($DB_SERVER && $DB_DATABASE) {
				if (!$db) {
					$e->add(t('Unable to connect to database.'));
				} else {					
					$num = $db->GetCol("show tables");
					if (count($num) > 0) {
						$e->add(t('There are already %s tables in this database. concrete5 must be installed in an empty database.', count($num)));
					}
				}
			}
		}	
		return $e;
	}
	
	public function reset() {
		// remove site.php so that we can try again ?
		return;
		if (is_resource($this->fp)) {
			fclose($this->fp);
		}
		if (file_exists(DIR_CONFIG_SITE . '/site_install.php')) {
			unlink(DIR_CONFIG_SITE . '/site_install.php');
		}
		if (file_exists(DIR_CONFIG_SITE . '/site_install_user.php')) {
			unlink(DIR_CONFIG_SITE . '/site_install_user.php');
		}

		if (file_exists(DIR_CONFIG_SITE . '/site.php')) {
			unlink(DIR_CONFIG_SITE . '/site.php');
		}
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
			
			$e = $this->validateDatabase($e);
			$e = $this->validateSampleContent($e);
			
			if ($val->test() && (!$e->has())) {


				// write the config file
				$vh = Loader::helper('validation/identifier');
				$salt = ( defined('MANUAL_PASSWORD_SALT') ) ? MANUAL_PASSWORD_SALT : $vh->getString(64);
				$this->fp = @fopen(DIR_CONFIG_SITE . '/site_install.php', 'w+');
				$this->fpu = @fopen(DIR_CONFIG_SITE . '/site_install_user.php', 'w+');
				if ($this->fp) {
					$configuration = "<?php\n";
					$configuration .= "define('DB_SERVER', '" . addslashes($_POST['DB_SERVER']) . "');\n";
					$configuration .= "define('DB_USERNAME', '" . addslashes($_POST['DB_USERNAME']) . "');\n";
					$configuration .= "define('DB_PASSWORD', '" . addslashes($_POST['DB_PASSWORD']) . "');\n";
					$configuration .= "define('DB_DATABASE', '" . addslashes($_POST['DB_DATABASE']) . "');\n";
					if (isset($setPermissionsModel)) {
						$configuration .= "define('PERMISSIONS_MODEL', '" . addslashes($setPermissionsModel) . "');\n";
					}
					$configuration .= "define('PASSWORD_SALT', '{$salt}');\n";
					if (is_array($_POST['SITE_CONFIG'])) {
						foreach($_POST['SITE_CONFIG'] as $key => $value) { 
							$configuration .= "define('" . $key . "', '" . $value . "');\n";
						}
					}
					$res = fwrite($this->fp, $configuration);
					fclose($this->fp);
					chmod(DIR_CONFIG_SITE . '/site_install.php', 0700);
				} else {
					throw new Exception(t('Unable to open config/site.php for writing.'));
				}

				if ($this->fpu) {
					$configuration = "<?php\n";
					$configuration .= "define('INSTALL_USER_EMAIL', '" . $_POST['uEmail'] . "');\n";
					$configuration .= "define('INSTALL_USER_PASSWORD_HASH', '" . User::encryptPassword($_POST['uPassword'], $salt) . "');\n";
					$configuration .= "define('INSTALL_STARTING_POINT', '" . $this->post('SAMPLE_CONTENT') . "');\n";
					$configuration .= "define('SITE', '" . addslashes($_POST['SITE']) . "');\n";
					if (defined('ACTIVE_LOCALE') && ACTIVE_LOCALE != '' && ACTIVE_LOCALE != 'en_US') {
						$configuration .= "define('ACTIVE_LOCALE', '" . ACTIVE_LOCALE . "');\n";
					}
					$res = fwrite($this->fpu, $configuration);
					fclose($this->fpu);
					chmod(DIR_CONFIG_SITE . '/site_install_user.php', 0700);
					if (PHP_SAPI != 'cli') {
						$this->redirect('/');
					}
				} else {
					throw new Exception(t('Unable to open config/site_user.php for writing.'));
				}

			
			} else {
				if ($e->has()) {
					$this->set('error', $e);
				} else {
					$this->set('error', $val->getError());
				}
			}
			
		} catch (Exception $e) {
			$this->reset();
			$this->set('error', $e);
		}
	}

}

