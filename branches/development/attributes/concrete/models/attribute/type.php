<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeType extends Object {

	public function getAttributeTypeID() {return $this->atID;}
	public function getAttributeTypeHandle() {return $this->atHandle;}
	public function getAttributeTypeName() {return $this->atName;}
	public function getController() {return $this->controller;}
	
	public static function getByID($atID) {
		$db = Loader::db();
		$row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atID = ?', array($atID));
		$at = new AttributeType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public static function getList() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select atID from AttributeTypes order by atID asc');
		while ($row = $r->FetchRow()) {
			$list[] = AttributeType::getByID($row['atID']);
		}
		return $list;
	}
	
	public static function getByHandle($atHandle) {
		
		// Handle legacy handles
		switch($atHandle) {
			case 'date':
				$atHandle = 'date_time';
				break;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atHandle = ?', array($atHandle));
		$at = new AttributeType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public static function add($atHandle, $atName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into AttributeTypes (atHandle, atName) values (?, ?)', array($atHandle, $atName));
		$id = $db->Insert_ID();
		return AttributeType::getByID($id);
	}
	
	public function getValue($avID) {
		$cnt = $this->getController();
		return $cnt->getValue($avID);
	}
	
	public function render($view, $ak = false, $value = false, $return = false) {
		// local scope
		Loader::library('attribute/view');
		$av = new AttributeTypeView($this, $ak, $value);	
		$resp = $av->render($view, $return);
		if ($return) {
			return $resp;
		}
	}
	
	protected function loadController() {
		// local scope
		$atHandle = $this->atHandle;
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->atHandle) . 'AttributeTypeController';
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . FILENAME_ATTRIBUTE_CONTROLLER)) {
			$file = DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . FILENAME_ATTRIBUTE_CONTROLLER;
		} else if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php')) {
			$file = DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php';
		}
		
		$pkgID = $row['pkgID'];
		if (!isset($file) && $pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
			if (file_exists($dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . FILENAME_ATTRIBUTE_CONTROLLER)) {
				$file = $dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . FILENAME_ATTRIBUTE_CONTROLLER;
			} else if (file_exists($dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php')) {
				$file = $dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php';
			}
		}
		
		if (!isset($file)) {
			if (file_exists(DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . FILENAME_ATTRIBUTE_CONTROLLER)) {
				$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . FILENAME_ATTRIBUTE_CONTROLLER;
			} else if (file_exists(DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php')) {
				$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php';
			}
		}
		if (!isset($file)) {
			$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/default/' . FILENAME_ATTRIBUTE_CONTROLLER;
			$className = 'DefaultAttributeTypeController';
		}
		require_once($file);
		$this->controller = new $className($this);
	}
	
}