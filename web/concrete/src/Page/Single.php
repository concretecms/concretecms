<?php
namespace Concrete\Core\Page;
use \Page as CorePage;
use Loader;
use Environment;
use Config;
use User;
use Package;

use CacheLocal;
/**
*
* SinglePage extends the page class for those instances of pages that have no type, and are special "single pages"
* within the system.
* @package Pages
*
*/
class Single {

	// These are pages that you're allowed to override with templates set in themes
	public static function getThemeableCorePages() {
		$themeableCorePages = array('download_file.php', 'login.php', 'maintenance.php', 'members.php', 'page_forbidden.php', 'page_not_found.php', 'register.php', 'upgrade.php', 'user_error.php');
		return $themeableCorePages;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$r = $db->Execute("select cID from Pages where cFilename is not null and pkgID = ?", array($pkg->getPackageID()));
		$singlePages = array();
		while ($row = $r->FetchRow()) {
			$singlePages[] = CorePage::getByID($row['cID']);
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
		$node = static::sanitizePath($node);
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

	public static function refresh(CorePage $c) {
		// takes a generated collection and refreshes it - updates its path, it's cDateModified
		// it's name, it's permissions

		if (!$c->isGeneratedCollection()) {
			return false;
		}

		$pkg = Package::getByID($c->getPackageID());
		$currentPath = $c->getCollectionPath();
		$pathToFile = static::getPathToNode($currentPath, $pkg);

		$txt = Loader::helper('text');

		$data = array();
		$data['cName'] = $txt->unhandle($c->getCollectionHandle());
		$data['cFilename'] = $pathToFile;

		$c->update($data);
		$env = Environment::get();
		$env->clearOverrideCache();

	}

	public static function getByID($cID, $version = 'RECENT') {
		$c = Page::getByID($cID, $version);
		return $c;
	}

	/*
	 * Adds a new single page at the given path, optionally specify a Package
	 * @param string $cPath
	 * @param Package $pkg
	 * @return Page
	 */
	public static function add($cPath, $pkg = null) {
		// if we get to this point, we create a special collection
		// without a specific type. This collection has a special cFilename that
		// points to the passed node
		$db = Loader::db();
		$txt = Loader::helper('text');
		Loader::helper('concrete/ui')->clearInterfaceItemsCache();

		// trim off a leading / if there is one
		$cPath = trim($cPath, '/');

		// now we grab the parent collection, if there is a static one.

		$pages = explode('/', $cPath);

		// instantiate the home collection so we have someplace to add these to
		$parent = CorePage::getByID(1);

		// now we iterate through the pages  to ensure that they exist in the system before adding the new guy

		$pathPrefix = '';

		for ($i = 0; $i < count($pages); $i++) {
			$currentPath = $pathPrefix . $pages[$i];

			$pathToFile = static::getPathToNode($currentPath, $pkg);

			// check to see if a page at this point in the tree exists
			$c = CorePage::getByPath("/" . $currentPath);
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


			} else {
				$parent = $c;
			}

			$pathPrefix = $currentPath . '/';
		}
		$env = Environment::get();
		$env->clearOverrideCache();
		return $newC;

	}

	// returns all pages in the site that are "single"
	public static function getList() {
		$db = Loader::db();
		$r = $db->query("select Pages.cID from Pages inner join Collections on Pages.cID = Collections.cID where cFilename is not null order by cDateModified desc");
		$pages = array();
		while ($row = $r->fetchRow()) {
			$c = Page::getByID($row['cID']);
			$pages[] = $c;
		}
		return $pages;
	}


}
