<?php
namespace Concrete\Core\Package;
use \Concrete\Core\Foundation\Object;
use Loader;
use CacheLocal;
class PackageList extends Object {

	protected $packages = array();

	public function add($pkg) {
		$this->packages[] = $pkg;
	}

	public function getPackages() {
		return $this->packages;
	}

	public static function export($xml) {
		$packages = static::get()->getPackages();
		$pkgs = $xml->addChild("packages");
		foreach($packages as $pkg) {
			$node = $pkgs->addChild('package');
			$node->addAttribute('handle', $pkg->getPackageHandle());
		}
	}

	public static function getHandle($pkgID) {
		if ($pkgID < 1) {
			return false;
		}
		$packageList = CacheLocal::getEntry('packageHandleList', false);
		if (is_array($packageList)) {
			return $packageList[$pkgID];
		}

		$packageList = array();
		$db = Loader::db();
		$r = $db->Execute('select pkgID, pkgHandle from Packages where pkgIsInstalled = 1');
		while ($row = $r->FetchRow()) {
			$packageList[$row['pkgID']] = $row['pkgHandle'];
		}

		CacheLocal::set('packageHandleList', false, $packageList);
		return $packageList[$pkgID];
	}

	public static function refreshCache() {
		CacheLocal::delete('packageHandleList', false);
		CacheLocal::delete('pkgList', 1);
		CacheLocal::delete('pkgList', 0);
	}

	public static function get($pkgIsInstalled = 1) {
		$pkgList = CacheLocal::getEntry('pkgList', $pkgIsInstalled);
		if ($pkgList != false) {
			return $pkgList;
		}

		$db = Loader::db();
		$r = $db->query("select pkgID, pkgName, pkgIsInstalled, pkgDescription, pkgVersion, pkgHandle, pkgDateInstalled from Packages where pkgIsInstalled = ? order by pkgID asc", array($pkgIsInstalled));
		$list = new static();
		while ($row = $r->fetchRow()) {
			$pkg = new Package;
			$pkg->setPropertiesFromArray($row);
			$list->add($pkg);
		}

		CacheLocal::set('pkgList', $pkgIsInstalled, $list);

		return $list;
	}

}
