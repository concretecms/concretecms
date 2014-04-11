<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message as ConversationMessage;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$as = Loader::helper('validation/antispam');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	$message = ConversationMessage::getByID($_POST['cnvMessageID']);
	if (is_object($message)) {
		$message->flag(ConversationFlagType::getByHandle('spam'));
		$message->unapprove();
		$as->report($message->getConversationMessageBody(),
					$message->getConversationMessageUserObject(),
					$message->getConversationMessageSubmitIP(),
					$message->getConversationMessageSubmitUserAgent()
				);
		Loader::element('conversation/message', array('message' => $message));
	}
}
