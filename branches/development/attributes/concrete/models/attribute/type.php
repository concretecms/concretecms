<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeType extends Object {

	public function getAttributeTypeID() {return $this->atID;}
	public function getAttributeTypeHandle() {return $this->atHandle;}
	public function getController() {return $this->controller;}
	
	public static function getByID($atID) {
		$db = Loader::db();
		$row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atID = ?', array($atID));
		$at = new AttributeType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public static function getByHandle($atHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atHandle = ?', array($atHandle));
		$at = new AttributeType();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public function getValue($avID) {
		$cnt = $this->getController();
		return $cnt->getValue($avID);
	}
	
	public function render($ak, $view, $value) {
		// local scope
		Loader::library('attribute/view');
		$av = new AttributeTypeView();
		$av->render($ak, $this, $view, $value);
	}
	
	/** 
	 * Adds a generic attribute record (with this type) to the AttributeValues table
	 */
	public function addAttributeValue() {
		$db = Loader::db();
		$u = new User();
		$dh = Loader::helper('date');
		$uID = $u->isRegistered() ? $u->getUserID() : 0;
		$avDate = $dh->getLocalDateTime();
		$v = array($this->atID, $uID, $avDate);
		$db->Execute('insert into AttributeValues (atID, uID, avDateAdded) values (?, ?, ?)', $v);
		$avID = $db->Insert_ID();
		return AttributeValue::getByID($avID);
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
			$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . 'default.php';
			$className = 'DefaultAttributeTypeController';
		}
		require_once($file);
		$this->controller = new $className($this);
	}
	
}