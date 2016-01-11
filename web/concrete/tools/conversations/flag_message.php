<?php

defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$as = Loader::helper('validation/antispam');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
    $message = ConversationMessage::getByID($_POST['cnvMessageID']);
    if (is_object($message)) {
        $mp = new Permissions($message);
        if ($mp->canFlagConversationMessage()) {
            $flagtype = ConversationFlagType::getByHandle('spam');
            $message->flag($flagtype);
            $message->unapprove();
            $author = $message->getConversationMessageAuthorObject();
            $email = $author->getEmail();
            $as->report($message->getConversationMessageBody(),
                $author->getName(),
                $author->getEmail(),
                $message->getConversationMessageSubmitIP(),
                $message->getConversationMessageSubmitUserAgent()
            );

            $r = Request::getInstance();
            $types = $r->getAcceptableContentTypes();
            if ($types[0] == 'application/json') {
                $r = new \Concrete\Core\Application\EditResponse();
                $r->setMessage(t('Message flagged successfully.'));
                $r->outputJSON();
            } else {
                Loader::element('conversation/message', array('message' => $message));
            }
        }
    }
}
