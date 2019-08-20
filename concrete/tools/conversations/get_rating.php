<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Conversation\Message\Message as ConversationMessage;
use Concrete\Core\Legacy\Loader;

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
    $msg = ConversationMessage::getByID($_POST['cnvMessageID']);
    echo $msg->getConversationMessageTotalRatingScore();
}
