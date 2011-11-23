<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceMenuHelper {

	protected $pageHeaderMenuItems = array();
	
	/** 
	 * Adds a menu item to the header menu area 
	 * <code>
	 * 	$bh->addMenuItem($menuItemID, $menuItemName, $positionInMenu, $linkAttributes, $pkgHandle = false);
	 * </code>
	 */
	public function addPageHeaderMenuItem($menuItemID, $menuItemName, $positionInMenu, $linkAttributes, $pkgHandle = false) {
		$obj = new ConcreteInterfaceHelperMenuItem($menuItemID, $menuItemName, $positionInMenu, $linkAttributes, $pkgHandle);
		$this->pageHeaderMenuItems[] = $obj;
	}
	
	/** 
	 * Returns current menu items
	 */
	public function getPageHeaderMenuItems($position = false) {
		if ($position) {
			$tmpItems = array();
			foreach($this->pageHeaderMenuItems as $mi) {
				if ($mi->getPosition() == $position) {
					$tmpItems[] = $mi;
				}
			}
			return $tmpItems;
		} else {
			return $this->pageHeaderMenuItems;
		}
	}

}

class ConcreteInterfaceHelperMenuItem {

	public function __construct($handle, $name, $position, $linkAttributes, $pkgHandle = false) {
		$this->handle = $handle;
		$this->name = $name;
		$this->position = $position;
		$this->linkAttributes = $linkAttributes;
		$this->pkgHandle = $pkgHandle;
	}
	
	protected $controller;
	
	public function getHandle() {return $this->handle;}
	public function getName() {return $this->name;}
	public function setName($name) {$this->name = $name;}
	public function getPosition() {return $this->position;}
	public function getLinkAttributes() {return $this->linkAttributes;}
	public function getPackageObject() {return $this->pkgHandle;}
	
	public function getController() {
		if (isset($this->controller)) {
			return $this->controller;
		} else {
			$class = Object::camelcase($this->handle . 'ConcreteInterfaceMenuItemController');
			if (!class_exists($class)) {
				$file1 = DIR_FILES_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $this->handle . '/' . FILENAME_MENU_ITEM_CONTROLLER;
				if ($this->pkgHandle) {
					$pkgHandle = $this->pkgHandle;
					$dir = (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
					$file2 = $dir . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $this->handle . '/' . FILENAME_MENU_ITEM_CONTROLLER;
				}
				$file3 = DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $this->handle . '/' . FILENAME_MENU_ITEM_CONTROLLER;
				if (file_exists($file1)) {
					include($file1);
				} else if (isset($file2) && file_exists($file2)) {
					include($file2);
				} else {
					include($file3);
				}
			}

			$this->controller = new $class();
			$this->controller->setMenuItem($this);
			return $this->controller;
		}
	}
	
	public function getMenuItemFilePath($_file) {
		$f = $this->mapMenuItemFilePath($_file);
		if (is_object($f)) {
			return $f->file;
		}
	}
	
	public function getMenuItemFileURL($_file) {
		$f = $this->mapMenuItemFilePath($_file);
		if (is_object($f)) {
			return $f->url;
		}
	}
	
	protected function mapMenuItemFilePath($_file) {
		$handle = $this->handle;
		if (file_exists(DIR_FILES_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file)) {
			$file = DIR_FILES_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file;
			$url = BASE_URL . DIR_REL . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file;
		}
		
		if ($this->pkgHandle) {
			if (!isset($file)) {
				$pkgHandle = $this->pkgHandle;
				$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
				if (file_exists($dirp . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file)) {
					$file = $dirp . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file;
					$url = BASE_URL . DIR_REL . '/' .DIRNAME_PACKAGES. '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file;
				}
			}
		}
		
		if (!isset($file)) {
			if (file_exists(DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' .  $handle . '/' . $_file)) {
				$file = DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' .  $handle . '/' . $_file;
				$url = ASSETS_URL . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_ELEMENTS_HEADER_MENU . '/' . $handle . '/' . $_file;
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
}

class ConcreteInterfaceMenuItemController extends Controller {
	
	protected $menuItem;
	protected $headerItemsToCheck = array(
		'CSS' => 'view.css', 
		'JAVASCRIPT' => 'view.js'
	);

	public function setMenuItem($obj) {
		$this->menuItem = $obj;
	}

	public function outputAutoHeaderItems() {
		$h = Loader::helper('html');
		if ($this->menuItem->getController()->displayItem()) {
			foreach($this->headerItemsToCheck as $t => $i) {
				$o = $this->menuItem->getMenuItemFilePath($i);
				if ($o) {
					$this->menuItem->getMenuItemFileURL($i);
					switch($t) {
						case 'CSS':
							$this->addHeaderItem($h->css($this->menuItem->getMenuItemFileURL($i)));
							break;
						case 'JAVASCRIPT':
							$this->addFooterItem($h->javascript($this->menuItem->getMenuItemFileURL($i)));
							break;
					}
				}
			}
		}
	}
	
	public function displayItem() {
		return true;
	}
	
	public function getMenuLinkHTML() {
		$attribs = '';
		if (is_array($this->menuItem->getLinkAttributes())) {
			foreach($this->menuItem->getLinkAttributes() as $key => $value) {
				if ($key == 'class') {
					$value = 'ccm-header-nav-package-item ' . $value;
				}
				$attribs .= $key . '="' . $value . '" ';
			}
		}
		$html = '<a id="ccm-page-edit-nav-' . $this->menuItem->getHandle() . '" ' . $attribs . '>' . $this->menuItem->getName() . '</a>';
		return $html;
	}
}