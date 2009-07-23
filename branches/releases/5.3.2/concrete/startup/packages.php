<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$pla = PackageList::get();
$pl = $pla->getPackages();

foreach($pl as $p) {
	if ($p->isPackageInstalled()) {
		$pkg = Loader::package($p->getPackageHandle());
		if (method_exists($pkg, 'on_start')) {
			call_user_func(array($pkg, 'on_start'));
		}
	}
}