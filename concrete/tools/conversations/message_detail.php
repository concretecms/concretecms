<?php

defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
if ($_POST['enablePosting']) {
    $enablePosting = true;
} else {
    $enablePosting = false;
}

if (in_array($_POST['displayMode'], array('flat'))) {
    $displayMode = $_POST['displayMode'];
} else {
    $displayMode = 'threaded';
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
    $message = ConversationMessage::getByID($_POST['cnvMessageID']);
    if (is_object($message)) {
        if ($message->isConversationMessageApproved()) {
            Loader::element('conversation/message', array('message' => $message, 'displayMode' => $displayMode, 'enablePosting' => $enablePosting, 'enableCommentRating' => $_POST['enableCommentRating']));
        } else {
            // it's a new message, but it's pending
            Loader::element('conversation/message/pending', array('message' => $message));
        }
    }
}
