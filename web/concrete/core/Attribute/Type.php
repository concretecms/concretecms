<?
namespace Concrete\Core\Attribute;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Attribute\View as AttributeTypeView;
use Loader;
use \Concrete\Core\Package\PackageList;
class Type extends Object {

	protected $atID;
	
	public function getAttributeTypeID() {return $this->atID;}
	public function getAttributeTypeHandle() {return $this->atHandle;}
	public function getAttributeTypeName() {return $this->atName;}
	public function getController() {return $this->controller;}

	/** Returns the display name for this attribute type (localized and escaped accordingly to $format)
	* @param string $format = 'html'
	*	Escape the result in html format (if $format is 'html').
	*	If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getAttributeTypeDisplayName($format = 'html') {
		$value = tc('AttributeTypeName', $this->getAttributeTypeName());
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}

	public static function getByID($atID) {
		$db = Loader::db();
		$row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atID = ?', array($atID));
		$at = new static();
		$at->setPropertiesFromArray($row);
		$at->loadController();
		return $at;
	}
	
	public function __destruct() {
		unset($this->controller);
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
			$list[] = static::getByID($row['atID']);
		}
		$r->Close();
		return $list;
	}
	
	public static function exportList($xml) {
		$attribs = static::getList();
		$db = Loader::db();
		$axml = $xml->addChild('attributetypes');
		foreach($attribs as $at) {
			$atype = $axml->addChild('attributetype');
			$atype->addAttribute('handle', $at->getAttributeTypeHandle());
			$atype->addAttribute('package', $at->getPackageHandle());
			$categories = $db->GetCol('select akCategoryHandle from AttributeKeyCategories inner join AttributeTypeCategories where AttributeKeyCategories.akCategoryID = AttributeTypeCategories.akCategoryID and AttributeTypeCategories.atID = ?', array($at->getAttributeTypeID()));
			if (count($categories) > 0) {
				$cat = $atype->addChild('categories');
				foreach($categories as $catHandle) {
					$cat->addChild('category')->addAttribute('handle', $catHandle);
				}
			}
		}
	}
	
	public function delete() {
		$db = Loader::db();
		if (method_exists($this->controller, 'deleteType')) {
			$this->controller->deleteType();
		}
		
		$db->Execute("delete from AttributeTypes where atID = ?", array($this->atID));
		$db->Execute("delete from AttributeTypeCategories where atID = ?", array($this->atID));
	}
	
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select atID from AttributeTypes where pkgID = ? order by atID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['atID']);
		}
		$r->Close();
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
		if ($row['atID']) {
			$at = new static();
			$at->setPropertiesFromArray($row);
			$at->loadController();
			return $at;
		}
	}
	
	public static function add($atHandle, $atName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into AttributeTypes (atHandle, atName, pkgID) values (?, ?, ?)', array($atHandle, $atName, $pkgID));
		$id = $db->Insert_ID();
		$est = static::getByID($id);
		
		$path = $est->getAttributeTypeFilePath(FILENAME_ATTRIBUTE_DB);
		if ($path) {
			Package::installDB($path);
		}
		return $est;
	}
	
	public function getValue($avID) {
		$cnt = $this->getController();
		return $cnt->getValue($avID);
	}
	
	public function render($view, $ak = false, $value = false, $return = false) {
		// local scope
		if ($value) {
			$av = new AttributeTypeView($value);
		} else if ($ak) {
			$av = new AttributeTypeView($ak);
		} else {
			$av = new AttributeTypeView($this);
		}
		ob_start();
		$av->render($view);
		$contents = ob_get_contents();
		ob_end_clean();
		if ($return) {
			return $contents;
		} else {
			print $contents;
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
	
	public function getAttributeTypeFilePath($_file) {
		$f = $this->mapAttributeTypeFilePath($_file);
		if (is_object($f)) {
			return $f->file;
		}
	}
	
	public function getAttributeTypeFileURL($_file) {
		$f = $this->mapAttributeTypeFilePath($_file);
		if (is_object($f)) {
			return $f->url;
		}
	}
	
	protected function mapAttributeTypeFilePath($_file) {
		$atHandle = $this->atHandle;
		if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $_file)) {
			$file = DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $_file;
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $_file;
		} else if ($_file == FILENAME_ATTRIBUTE_CONTROLLER && file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php')) {
			$file = DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php';
		}
		
		$pkgID = $this->pkgID;
		if (!isset($file) && $pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
			if (file_exists($dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $_file)) {
				$file = $dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $_file;
				$url = BASE_URL . DIR_REL . '/' .DIRNAME_PACKAGES. '/' .$pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $_file;
			} else if ($_file == FILENAME_ATTRIBUTE_CONTROLLER && file_exists($dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php')) {
				$file = $dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php';
			}
		}
		
		if (!isset($file)) {
			if (file_exists(DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $_file)) {
				$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $_file;
				$url = ASSETS_URL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $_file;
			} else if ($_file == FILENAME_ATTRIBUTE_CONTROLLER && file_exists(DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php')) {
				$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '.php';
			}
		}
		
		if (isset($file)) {
			$obj = new stdClass;
			$obj->file = $file;
			$obj->url = $url;
			return $obj;
		} else {
			return false;
		}
	}
	
	public function loadController() { 
		$txt = Loader::helper('text');
		$className = \Concrete\Core\Foundation\ClassLoader::getClassName('Attribute\\' . $txt->camelcase($this->atHandle) . '\\Controller');
		$this->controller = new $className($this);
	}
	
}