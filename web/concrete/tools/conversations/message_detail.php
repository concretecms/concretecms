<?
defined('C5_EXECUTE') or die("Access Denied.");
$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
if ($_POST['enablePosting']) {
	$enablePosting = true;
} else {
	$enablePosting = false;
}
if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	$message = ConversationMessage::getByID($_POST['cnvMessageID']);
	if (is_object($message)) { 
		Loader::element('conversation/message', array('message' => $message, 'enablePosting' => $enablePosting));
	}
}
