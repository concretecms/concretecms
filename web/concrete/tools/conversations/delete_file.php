<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageAttachmentID']) && $_POST['cnvMessageAttachmentID'] > 0) {
	$attachment = ConversationMessage::getAttachmentByID($_POST['cnvMessageAttachmentID']);
	
	$message = ConversationMessage::getByID($attachment->cnvMessageID);
    $mp = new Permissions($message);
    if ($mp->canEditConversationMessage()) {
        if (is_object($attachment)) {
            $message->removeFile($_POST['cnvMessageAttachmentID']);
        }
        $attachmentDeleted = new stdClass();
        $attachmentDeleted->attachmentID = $_POST['cnvMessageAttachmentID'];
        echo Loader::helper('json')->encode($attachmentDeleted);
    }
}
