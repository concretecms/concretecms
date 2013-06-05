<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('newsflow');
if (Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
	$ed = Newsflow::getEditionByID($_REQUEST['cID']);
	if (is_object($ed)) {
		print $ed->getContent();
	}
} else if (isset($_REQUEST['cPath'])) {
	$ed = Newsflow::getEditionByPath($_REQUEST['cPath']);
	if (is_object($ed)) {
		print $ed->getContent();
	}
}
