<?
defined('C5_EXECUTE') or die("Access Denied.");
$pla = PackageList::get();
$pl = $pla->getPackages();

foreach($pl as $p) {
	if ($p->isPackageInstalled()) {
		$pkg = Loader::package($p->getPackageHandle());
		if (is_object($pkg)) {
			$pkg->setupPackageLocalization();
			if (method_exists($pkg, 'on_start')) {
				$pkg->on_start();
			}
		}
	}
}