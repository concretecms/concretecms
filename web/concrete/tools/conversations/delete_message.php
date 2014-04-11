<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message as ConversationMessage;
$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	$message = ConversationMessage::getByID($_POST['cnvMessageID']);
	if (is_object($message)) { 
		$message->delete();
		Loader::element('conversation/message', array('message' => $message));
	}
}
