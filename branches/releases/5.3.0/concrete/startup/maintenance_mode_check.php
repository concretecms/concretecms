<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
if ((!$c->isAdminArea()) && ($c->getCollectionPath() != '/login')) {

	$smm = Config::get('SITE_MAINTENANCE_MODE');
	if ($smm == 1) {
		$v = View::getInstance();
		$v->setTheme('concrete');
		$v->render('/maintenance_mode/');
		exit;
	}
	
}