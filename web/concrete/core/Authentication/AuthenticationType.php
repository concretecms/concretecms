<?php 
namespace Concrete\Core\Authentication;
use \Concrete\Core\Foundation\Object;
use Exception;
use Package;
use Loader;
use \Concrete\Core\Package\PackageList;
use Core;

class AuthenticationType extends Object {

	public function getAuthenticationTypeID() {return $this->authTypeID;}
	public function getAuthenticationTypeHandle() {return $this->authTypeHandle;}
	public function getAuthenticationTypeName() {return $this->authTypeName;}
	public function getAuthenticationTypeStatus() {return $this->authTypeIsEnabled;}
	public function getAuthenticationTypeDisplayOrder() {return $this->authTypeDisplayOrder;}
	public function getAuthenticationTypePackageID() {return $this->pkgID;}
	public function isEnabled() {return !!$this->getAuthenticationTypeStatus();}
	public function getController() {return $this->controller;}


	/**
	 * AuthenticationType::setAuthenticationTypeDisplayOrder
	 * Update the order for display.
	 *
	 * @param int $order value from 0-n to signify order.
	 */
	public function setAuthenticationTypeDisplayOrder($order) {
		$db = Loader::db();
		$db->Execute('UPDATE AuthenticationTypes SET authTypeDisplayOrder=? WHERE authTypeID=?',array($order,$this->getAuthenticationTypeID()));
	}


	/**
	 * @param int $authTypeID
	 * @return Concrete5_Model_AuthenticationType
	 */
	public static function getByID($authTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT * FROM AuthenticationTypes where authTypeID=?', array($authTypeID));
		if (!$row) {
			throw new Exception(t('Invalid Authentication Type ID'));
		}
		$at = AuthenticationType::load($row);
		$at->loadController();
		return $at;
	}

	/**
	 * AuthenticationType::load
	 * Load an AuthenticationType from an array.
	 *
	 * @param array $arr Array of raw sql data.
	 */
	public static function load($arr) {
		$extract = array('authTypeID','authTypeName','authTypeHandle','authTypeHandle','authTypeName','authTypeDisplayOrder','authTypeIsEnabled','pkgID');
		$obj = new AuthenticationType;
		foreach ($extract as $key) {
			if (!isset($arr[$key])) {
				return false;
			}
			$obj->{$key} = $arr[$key];
		}
		$obj->loadController();
		return $obj;
	}

	/**
	 * AuthenticationType::getList
	 * Return a raw list of authentication types, sorted by either installed order or display order.
	 *
	 * @param bool $sorted true: Sort by installed order, false: Sort by display order
	 */
	public static function getList($sorted=false) {
		$list = array();
		$db = Loader::db();
		$q = $db->query("SELECT * FROM AuthenticationTypes".($sorted?" ORDER BY authTypeDisplayOrder":""));
		while ($row = $q->fetchRow()) {
			$list[] = AuthenticationType::load($row);
		}
		return $list;
	}
	public static function getListSorted() {
		return AuthenticationType::getList(true);
	}

	/**
	 * AuthenticationType::getActiveList
	 * Return a raw list of /ACTIVE/ authentication types, sorted by either installed order or display order.
	 *
	 * @param bool $sorted true: Sort by installed order, false: Sort by display order
	 */
	public static function getActiveList($sorted=false) {
		$list = array();
		$db = Loader::db();
		$q = $db->query("SELECT * FROM AuthenticationTypes WHERE authTypeIsEnabled=1".($sorted?" ORDER BY authTypeDisplayOrder":""));
		while ($row = $q->fetchRow()) {
			$list[] = AuthenticationType::load($row);
		}
		return $list;
	}
	public static function getActiveListSorted() {
		return AuthenticationType::getActiveList(true);
	}

	/**
	 * AuthenticationType::disable
	 * Disable an authentication type.
	 */
	public function disable() {
		if ($this->getAuthenticationTypeID() == 1) {
			throw new Exception(t('The core concrete5 authentication cannot be disabled.'));
		}
		$db = Loader::db();
		$db->Execute('UPDATE AuthenticationTypes SET authTypeIsEnabled=0 WHERE AuthTypeID=?',array($this->getAuthenticationTypeID()));
	}

	/**
	 * AuthenticationType::enable
	 * Enable an authentication type.
	 */
	public function enable() {
		$db = Loader::db();
		$db->Execute('UPDATE AuthenticationTypes SET authTypeIsEnabled=1 WHERE AuthTypeID=?',array($this->getAuthenticationTypeID()));
	}

	/**
	 * AuthenticationType::toggle
	 * Toggle the active state of an AuthenticationType
	 */
	public function toggle() {
		return ($this->isEnabled() ? $this->disable() : $this->enable());
	}

	/**
	 * AuthenticationType::delete
	 * Remove an AuthenticationType, this should be used sparingly.
	 */
	public function delete() {
		$db = Loader::db();
		if (method_exists($this->controller, 'deleteType')) {
			$this->controller->deleteType();
		}

		$db->Execute("DELETE FROM AuthenticationTypes WHERE authTypeID=?", array($this->authTypeID));
	}

	/**
	 * AuthenticationType::getListByPackage
	 * Return a list of AuthenticationTypes that are associated with a specific package.
	 *
	 * @param Package $pkg
	 */
	public static function getListByPackage(Package $pkg) {
		$db = Loader::db();
		$list = array();

		$q = $db->query('SELECT * FROM AuthenticationTypes WHERE pkgID=?', array($pkg->getPackageID()));
		while ($row = $q->FetchRow()) {
			$list[] = AuthenticationType::load($row);
		}
		$r->Close();
		return $list;
	}

	/**
	 * AuthenticationType::getPackageHandle
	 * Return the package handle.
	 */
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	/**
	 * AuthenticationType::getByHandle
	 * Return loaded AuthenticationType with the given handle.
	 *
	 * @param string $atHandle AuthenticationType handle.
	 */
	public static function getByHandle($atHandle) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT * FROM AuthenticationTypes WHERE authTypeHandle=?', array($atHandle));
		if (!$row) {
			throw new Exception(t('Invalid Authentication Type Handle'));
		}
		$at = AuthenticationType::load($row);
		return $at;
	}

	/**
	 * AuthenticationType::add
	 *
	 * @param	string 	$atHandle	New AuthenticationType handle
	 * @param	string	$atName		New AuthenticationType name, expect this to be presented with "%s Authentication Type"
	 * @param	int		$order		Order int, used to order the display of AuthenticationTypes
	 * @param	Package	$pkg		Package object to which this AuthenticationType is associated.
	 * @return	AuthenticationType	Returns a loaded authentication type.
	 */
	public static function add($atHandle, $atName, $order=0, $pkg=false) {
		$die = true;
		try {
			AuthenticationType::getByHandle($atHandle);
		} catch (exception $e) {
			$die = false;
		}
		if ($die) {
			throw new Exception(t('Authentication type with handle %s already exists!', $atHandle));
		}

		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('INSERT INTO AuthenticationTypes (authTypeHandle, authTypeName, authTypeIsEnabled, authTypeDisplayOrder, pkgID) values (?, ?, ?, ?, ?)',
			array($atHandle, $atName, 1, intval($order), $pkgID));
		$id = $db->Insert_ID();
		$est = AuthenticationType::getByID($id);
		$path = $est->mapAuthenticationTypeFilePath(FILENAME_AUTHENTICATION_DB);
		if ($path) {
			Package::installDB($path);
		}

		return $est;
	}

	/**
	 * AuthenticationType::getAuthenticationTypeFilePath
	 * Return the path to a file, this is always BASE_URL.DIR_REL.FILE
	 *
	 * @param string $_file the relative path to the file.
	 */
	public function getAuthenticationTypeFilePath($_file) {
		$f = $this->mapAuthenticationTypeFilePath($_file);
		if ($f) {
			return BASE_URL.DIR_REL.$f;
		}
		return false;
	}

	/**
	 * AuthenticationType::mapAuthenticationTypeFilePath
	 * Return the first existing file path in this order:
	 *  - /models/authentication/types/HANDLE
	 *  - /packages/PKGHANDLE/authentication/types/HANDLE
	 *  - /concrete/models/authentication/types/HANDLE
	 *  - /concrete/core/models/authentication/types/HANDLE
	 *
	 * @param string $_file The filename you want.
	 * @return string This will return false if the file is not found.
	 */
	public function mapAuthenticationTypeFilePath($_file) {
		$atHandle = $this->getAuthenticationTypeHandle();

		$locations = array();
		$locations[] = implode('/',array(DIR_BASE,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle,$_file));
		if ($_file == FILENAME_AUTHENTICATION_CONTROLLER) {
			$locations[] = implode('/',array(DIR_BASE,DIRNAME_AUTHENTICATION,DIRNAME_AUTHENTICATION_TYPES,$atHandle)).".php";
		}

		if ($this->pkgID > 0) {
			$pkgHandle = PackageList::getHandle($this->pkgID);
			$dirp = is_dir(DIR_PACKAGES.'/'.$pkgHandle)?DIR_PACKAGES.'/'.$pkgHandle:DIR_PACKAGES_CORE.'/'.$pkgHandle;
			$locations[] = implode('/',array($dirp,DIRNAME_AUTHENTICATION,$atHandle,$_file));
			if ($_file == FILENAME_AUTHENTICATION_CONTROLLER) {
				$locations[] = implode('/',array($dirp,DIRNAME_AUTHENTICATION,$atHandle)).".php";
			}
		}

		$locations[] = implode('/',array(DIR_BASE_CORE,DIRNAME_AUTHENTICATION,$atHandle,$_file));
		if ($_file == FILENAME_AUTHENTICATION_CONTROLLER) {
			$locations[] = implode('/',array(DIR_BASE_CORE,DIRNAME_AUTHENTICATION,$atHandle)).".php";
		}

		foreach($locations as $location) {
			if (file_exists($location)) {
				return $location;
			}
		}
		return false;
	}

	/**
	 * AuthenticationType::renderTypeForm
	 * Render the settings form for this type.
	 * Settings forms are expected to handle their own submissions and redirect to the appropriate page.
	 * Otherwise, if the method exists, all $_REQUEST variables with the arrangement: HANDLE[]
	 * in an array to the AuthenticationTypeController::saveTypeForm
	 */
	public function renderTypeForm() {
		$form = $this->mapAuthenticationTypeFilePath('type_form.php');
		if ($form) {
			ob_start();
			$this->controller->edit();
			extract($this->controller->getSets());
			require_once($this->mapAuthenticationTypeFilePath('type_form.php')); // We use the $this method to prevent extract overwrite.
			$out = ob_get_contents();
			ob_end_clean();
			echo $out;
		} else {
			echo "<p>".t("This authentication type does not require any customization.")."</p>";
		}
	}

	/**
	 * AuthenticationType::renderForm
	 * Render the login form for this authentication type.
	 */
	public function renderForm($element = 'form') {
		$form = $this->mapAuthenticationTypeFilePath($element.'.php');
		if ($form) {
			ob_start();
			$this->controller->view();
			extract($this->controller->getSets());
			require_once($this->mapAuthenticationTypeFilePath($element.'.php'));
			$out = ob_get_contents();
			ob_end_clean();
			echo $out;
		}
	}

	/**
	 * AuthenticationType::renderHook
	 * Render the hook form for saving the profile settings.
	 * All settings are expected to be saved by each individual authentication type
	 */
	public function renderHook() {
		$form = $this->mapAuthenticationTypeFilePath('hook.php');
		if ($form) {
			ob_start();
			$this->controller->hook();
			extract($this->controller->getSets());
			require_once($this->mapAuthenticationTypeFilePath('hook.php'));
			$out = ob_get_contents();
			ob_end_clean();
			echo $out;
		}
	}

	/**
	 * AuthenticationType::loadController
	 * Load the AuthenticationTypeController into the AuthenticationType
	 */
	protected function loadController() {
		// local scope
		$authTypeHandle = Core::make('helper/text')->camelcase($this->authTypeHandle);
		$class = core_class('Authentication\\' . $authTypeHandle . '\\Controller');
		$this->controller = Core::make($class, array($this));
	}

}
