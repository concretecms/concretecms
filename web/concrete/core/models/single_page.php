<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* SinglePage extends the page class for those instances of pages that have no type, and are special "single pages"
* within the system.
* @package Pages
*
*/
class Concrete5_Model_SinglePage extends Page {

	// These are pages that you're allowed to override with templates set in themes
	public static function getThemeableCorePages() {
		$themeableCorePages = array('login.php', 'register.php');
		return $themeableCorePages;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$r = $db->Execute("select cID from Pages where cFilename is not null and pkgID = ?", $pkg->getPackageID());
		$singlePages = array();
		while ($row = $r->FetchRow()) {
			$singlePages[] = SinglePage::getByID($row['cID']);
		}
		return $singlePages;
	}
		
 	public static function sanitizePath($path) {
		//takes a damn cpath and returns no first slash, and no more than 1 intermediate slash in
		// the middle at any point
		$node = preg_replace("/([\/]+)/", "/", $path);
		if (substr($node, 0, 1) == "/") {
			$node = substr($node, 1);
		}
		// now do the same for the last node
		if (substr($node, strlen($node) - 1, 1) == '/') {
			$node = substr($node, 0, strlen($node) -1);
		}
		return $node;
	}
	
	public static function getPathToNode($node, $pkg) {
		$node = SinglePage::sanitizePath($node);
		// checks to see whether a passed $node is a static content node
		// (static content nodes exist within the views directory)
		
		// first, we look to see if the exact path exists (plus .php)
		$pathToFile = null;
		if (is_object($pkg)) {
			if (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) {
				$dirp = DIR_PACKAGES . '/' . $pkg->getPackageHandle();
			} else {
				$dirp = DIR_PACKAGES_CORE . '/' . $pkg->getPackageHandle();
			}
			
			$file1 = $dirp . '/' . DIRNAME_PAGES . '/' . $node . '/' . FILENAME_COLLECTION_VIEW;
			$file2 = $dirp . '/' . DIRNAME_PAGES . '/' . $node . '.php';
		} else {
			$file1 = DIR_FILES_CONTENT . '/' . $node . '/' . FILENAME_COLLECTION_VIEW;
			$file2 = DIR_FILES_CONTENT . '/' . $node . '.php';
			$file3 = DIR_FILES_CONTENT_REQUIRED . '/' . $node . '/' . FILENAME_COLLECTION_VIEW;
			$file4 = DIR_FILES_CONTENT_REQUIRED . '/' . $node . '.php';
		}

		if (file_exists($file1)) {
			$pathToFile = "/{$node}/" . FILENAME_COLLECTION_VIEW;
		} else if (file_exists($file2)) {
			$pathToFile = "/{$node}.php";
		} else if (isset($file3) && file_exists($file3)) {
			$pathToFile = "/{$node}/" . FILENAME_COLLECTION_VIEW;
		} else if (isset($file4) && file_exists($file4)) {
			$pathToFile = "/{$node}.php";
		}
		
		if (!$pathToFile) {
			$pathToFile = false;
		}
				
		return $pathToFile;

	}
	
	public function refresh() {
		// takes a generated collection and refreshes it - updates its path, it's cDateModified
		// it's name, it's permissions
		
		if (!$this->isGeneratedCollection()) {
			return false;
		}
		
		$pkg = Package::getByID($this->getPackageID());
		$currentPath = $this->getCollectionPath();
		$pathToFile = SinglePage::getPathToNode($currentPath, $pkg);
		$pxml = SinglePage::obtainPermissionsXML($currentPath, $pkg);

		$txt = Loader::helper('text');

		$data = array();
		$data['cName'] = $txt->unhandle($this->getCollectionHandle());
		$data['cFilename'] = $pathToFile;
		
		$this->update($data);	
		if ($pxml) {
			$this->assignPermissionSet($pxml); // pass it an array
		}
		$env = Environment::get();
		$env->clearOverrideCache();

	}

	public static function getByID($cID, $version = 'RECENT') {
		$where = "where Pages.cID = ? and Pages.cFilename is not null";
		$c = new SinglePage;
		$c->populatePage($cID, $where, $version);
		return $c;
	}
	
	/* 
	 * Adds a new single page at the given path, optionally specify a Package
	 * @param string $cPath
	 * @param Package $pkg
	 * @return Page
	 */
	public function add($cPath, $pkg = null) {
		// if we get to this point, we create a special collection 
		// without a specific type. This collection has a special cFilename that
		// points to the passed node
		$db = Loader::db();
		$txt = Loader::helper('text');
		Loader::helper('concrete/interface')->clearInterfaceItemsCache();
		
		// trim off a leading / if there is one
		$cPath = trim($cPath, '/');
		
		// now we grab the parent collection, if there is a static one. 
		
		$pages = explode('/', $cPath);
		
		// instantiate the home collection so we have someplace to add these to
		$parent = Page::getByID(1);
		
		// now we iterate through the pages  to ensure that they exist in the system before adding the new guy
		
		$pathPrefix = '';
		
		for ($i = 0; $i < count($pages); $i++) {
			$currentPath = $pathPrefix . $pages[$i];
			
			$pathToFile = SinglePage::getPathToNode($currentPath, $pkg);

			// check to see if a page at this point in the tree exists
			$c = Page::getByPath("/" . $currentPath);
			if ($c->isError() && $c->getError() == COLLECTION_NOT_FOUND) {
				// create the page at that point in the tree
			
				$data = array();
				$data['handle'] = $pages[$i];
				$data['name'] = $txt->unhandle($data['handle']);
				$data['filename'] = $pathToFile;
				$data['uID'] = USER_SUPER_ID;
				if ($pkg != null) {
					$data['pkgID'] = $pkg->getPackageID();
				}
				
				$newC = $parent->addStatic($data);	
				$parent = $newC;
				
				$pxml = SinglePage::obtainPermissionsXML($currentPath, $pkg);
				
				if ($pxml) {
					$newC->assignPermissionSet($pxml); // pass it an array
				}					
				
			} else {
				$parent = $c;
			}				
			
			$pathPrefix = $currentPath . '/';
		}
		$env = Environment::get();
		$env->clearOverrideCache();
		return $newC;
		
	}
	
	private static function checkPermissionsXML($doc, $node) {
		$dom = simplexml_load_file($doc);
		$n = $dom->xpath('//node[@handle=\'' . $node . '\']');
		if ($n != false) {
			return $n[0];
		}
		return null;
	}
	
	public static function obtainPermissionsXML($node, $pkg = null) {
		// this function reads a file in, and grabs all the various filesystem permissions xml that applies to that file
		// and returns it in a DOM object
		
		$node = SinglePage::sanitizePath($node);
		
		
		// first, we operate on this if it's not in a package
		
		if (!is_object($pkg)) {
			
			if (is_dir(DIR_FILES_CONTROLLERS . '/' . $node) || is_dir(DIR_FILES_CONTROLLERS_REQUIRED . '/' . $node)) {
				if (is_dir(DIR_FILES_CONTROLLERS . '/' . $node)) {
					$pathToPerms = DIR_FILES_CONTROLLERS . '/' . $node;
					if (file_exists($pathToPerms . '/' . FILENAME_COLLECTION_ACCESS)) {
						$xmlweb = $pathToPerms . '/' . FILENAME_COLLECTION_ACCESS;
					}
				}
				
				if (is_dir(DIR_FILES_CONTROLLERS_REQUIRED . '/' . $node)) {
					$pathToPerms = DIR_FILES_CONTROLLERS_REQUIRED . '/' . $node;
					if (file_exists($pathToPerms . '/' . FILENAME_COLLECTION_ACCESS)) {
						$xmlcore = $pathToPerms . '/' . FILENAME_COLLECTION_ACCESS;
					}
				}
			} else {
				if (strpos($node, '/') === false) {
					if (file_exists(DIR_FILES_CONTROLLERS . '/' . FILENAME_COLLECTION_ACCESS)) {
						$xmlweb = DIR_FILES_CONTROLLERS . '/' . FILENAME_COLLECTION_ACCESS;
					} else if (file_exists(DIR_FILES_CONTROLLERS_REQUIRED . '/' . FILENAME_COLLECTION_ACCESS)) {
						$xmlcore = DIR_FILES_CONTROLLERS_REQUIRED . '/' . FILENAME_COLLECTION_ACCESS;
					}
				}			
			}
				
			
			if (isset($xmlweb)) {
				$perms = SinglePage::checkPermissionsXML($xmlweb, $node);
				if ($perms != null) {
					return $perms;
				}
			} 

			
			if (isset($xmlcore)) {
				$perms = SinglePage::checkPermissionsXML($xmlcore, $node);
				if ($perms != null) {
					return $perms;
				}
			}

		} else {
		
			if (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) {
				$dirp = DIR_PACKAGES;			
			} else {
				$dirp = DIR_PACKAGES_CORE;
			}

			$file1 = $dirp . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_PAGES . '/' . $node . '/' . FILENAME_COLLECTION_VIEW;
			$file2 = $dirp . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_PAGES . '/' . $node . '.php';
			if (file_exists($file1)) {
				$pathToPerms = $dirp . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . '/' . $node;
			} else if (file_exists($file2)) {
				$pathNode = '/' . substr($node, 0, strrpos($node, '/'));
				$pathToPerms = $dirp . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_CONTROLLERS . $pathNode;
			}
			
			if (file_exists($pathToPerms . '/' . FILENAME_COLLECTION_ACCESS)) {
				$xml = $pathToPerms . '/' . FILENAME_COLLECTION_ACCESS;
			}
			
			if (isset($xml)) {
				$perms = SinglePage::checkPermissionsXML($xml, $node);
				if ($perms != null) {
					return $perms;
				}
			}
		}

		return false;
	}
	
	// returns all pages in the site that are "single" 
	public static function getList() {
		$db = Loader::db();
		$r = $db->query("select Pages.cID from Pages inner join Collections on Pages.cID = Collections.cID where cFilename is not null order by cDateModified desc");
		$pages = array();
		while ($row = $r->fetchRow()) {
			$p = new SinglePage;
			$p->populatePage($row['cID'], 'where Pages.cID = ?', 'RECENT');
			$pages[] = $p;
		}
		return $pages;
	}

	
}
