<?
defined('C5_EXECUTE') or die("Access Denied.");

if (Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
	$ed = Newsflow::getInstance()->getEditionByID($_REQUEST['cID']);
	if ($ed !== false) {
		print $ed->getContent();
	}
} else if (isset($_REQUEST['cPath'])) {
	$ed = Newsflow::getInstance()->getEditionByPath($_REQUEST['cPath']);
	if ($ed !== false) {
		print $ed->getContent();
	}
}
