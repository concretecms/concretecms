<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model("advertisement_details", "advertisement");
//Permissions Check
if($_GET['aID']) {
	$a = new AdvertisementDetails();
	$a->load("aID=".$_GET['aID']);
	$a->generateClick();
	header("Location: ".$a->getUrl());
	exit;		
} else { 	
	$v = View::getInstance();
	$v->renderError('Permission Denied',"You don't have permission to access this file");
	exit;
}
exit;
?>