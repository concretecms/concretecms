<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$as = Loader::helper('validation/antispam');

if (!Loader::helper('validation/token')->validate('add_conversation_message', $_POST['token'])) {
    $ve->add(t('Invalid conversation post token.'));
}
if (!$vs->notempty($_POST['cnvMessageBody'])) {
    $ve->add(t('Your message cannot be empty.'));
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	$message = ConversationMessage::getByID($_POST['cnvMessageID']);

	if (!is_object($message)) {
        $ve->add(t('Invalid message object.'));
    } else {
        $mp = new Permissions($message);
        if (!$mp->canEditConversationMessage()) {
            $ve->add(t('You do not have access to edit this message.'));
        }
    }
}

if (!$ve->has()) {
    $message->setMessageBody($_POST['cnvMessageBody']);
    $ax->sendResult($message);
} else {
    $ax->sendError($ve);
}
