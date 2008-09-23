<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
require(dirname(__FILE__) . '/../controller.php');

$previewMode = true;
$nh = Loader::helper('navigation');
$c = Page::getByID($_REQUEST['cID'], "ACTIVE");

$controller = new AutonavBlockController($c);


$controller->orderBy	= $_REQUEST['orderBy'];
$controller->displayPages 	= $_REQUEST['displayPages'];
$controller->displaySubPages 		= $_REQUEST['displaySubPages'];
$controller->displaySubPageLevels 		= $_REQUEST['displaySubPageLevels'];
$controller->displaySubPageLevelsNum 		= $_REQUEST['displaySubPageLevelsNum'];
$controller->displayUnavailablePages 		= $_REQUEST['displayUnavailablePages'];

if($controller->displayPages == "custom") {
	$controller->displayPagesCID = $_REQUEST['displayPagesCID'];
	$controller->displayPagesIncludeSelf = $_REQUEST['displayPagesIncludeSelf'];
}

//echo var_dump($cArray);
require(dirname(__FILE__) . '/../view.php');
exit;