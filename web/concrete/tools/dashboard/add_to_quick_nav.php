<?
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('validation/numbers');
if ($ih->integer($_REQUEST['cID'])) {
	$c = Page::getByID($_REQUEST['cID']);
