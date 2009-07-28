<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeType extends Object {

	protected function load($atID) {
		$db = Loader::db();
		$row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atID = ?', array($atID));
		$this->setPropertiesFromArray($row);
		return $akt;
	}

	public static function getByHandle($atHandle) {
		$txt = Loader::helper('text');
		$class = $txt->camelcase($atHandle) . 'AttributeType';
		$db = Loader::db();
		$row = $db->GetRow("select atID, pkgID from AttributeTypes where atHandle = ?", array($atHandle));
		
		// local scope
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
			$className = 'AttributeType';
		}
		
		$at = new $className;
		$at->load($row['atID']);
		return $at;
	}
	
	/** 
	 * Default getValue. Typically overridden by implementations but if the attribute doesn't need its own 
	 * special table we'll just use this one
	 */
	public function getValue($avID) {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDefault where avID = ?", array($avID));
		return $value;	
	}

}
