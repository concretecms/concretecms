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
	
	public static function getList($akCategoryHandle = false) {
		$db = Loader::db();
		$list = array();
		if ($akCategoryHandle == false) {
			$r = $db->Execute('select atID from AttributeTypes order by atID asc');
		} else {
			$r = $db->Execute('select atID from AttributeTypeCategories inner join AttributeKeyCategories on AttributeTypeCategories.akCategoryID = AttributeKeyCategories.akCategoryID and AttributeKeyCategories.akCategoryHandle = ? order by atID asc', array($akCategoryHandle));
		}
		
		while ($row = $r->FetchRow()) {
			$list[] = AttributeType::getByID($row['atID']);
		}
		return $list;
	}
	
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public function isAssociatedWithCategory($cat) {
		$db = Loader::db();
		$r = $db->GetOne("select count(akCategoryID) from AttributeTypeCategories where akCategoryID = ? and atID = ?", array($cat->getAttributeKeyCategoryID(), $this->getAttributeTypeID()));
		return $r > 0;
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
	
	public function getAttributeTypeIconSRC() {
		$ff = '/' . FILENAME_BLOCK_ICON;
		if ($this->getPackageID() > 0) {
			$db = Loader::db();
			$h = $this->getPackageHandle();
			$url = (is_dir(DIR_PACKAGES . '/' . $h)) ? BASE_URL . DIR_REL : ASSETS_URL; 
			$url = $url . '/' . DIRNAME_PACKAGES . '/' . $h . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $this->getAttributeTypeHandle() . $ff;
		} else if (file_exists(DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $this->getAttributeTypeHandle() . $ff)) {
			$url = ASSETS_URL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $this->getAttributeTypeHandle() . $ff;
		} else if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $this->getAttributeTypeHandle() . $ff)) {
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $this->getAttributeTypeHandle() . $ff;
		} else {
			$url = ASSETS_URL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/default' . $ff;		
		}
		return $url;
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

class PendingAttributeType extends AttributeType {

	public static function getList() {
		$db = Loader::db();
		$atHandles = $db->GetCol("select atHandle from AttributeTypes");
		
		$dh = Loader::helper('file');
		$available = array();
		if (is_dir(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES)) {
			$contents = $dh->getDirectoryContents(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES);
			foreach($contents as $atHandle) {
				if (!in_array($atHandle, $atHandles)) {
					$available[] = PendingAttributeType::getByHandle($atHandle);
				}
			}
		}
		return $available;
	}

	public static function getByHandle($atHandle) {
		$th = Loader::helper('text');
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle)) {
			$at = new PendingAttributeType();
			$at->atID = 0;
			$at->atHandle = $atHandle;
			$at->atName = $th->unhandle($atHandle);
			return $at;
		}
	}
	
	public function install() {
		$at = parent::add($this->atHandle, $this->atName);
		return $at;
	}

}