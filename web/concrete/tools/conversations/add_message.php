<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$u  = new User;
$pageObj = Page::getByID($_POST['cID']);
$areaObj = Area::get($pageObj, $_POST['blockAreaHandle']);
$blockObj = Block::getByID($_POST['bID'], $pageObj, $areaObj);
$cnvMessageSubject = null;

if(!is_object($blockObj)) {
	$ve->add(t('Invalid Block Object.'));
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvID'])) {
	$cn = Conversation::getByID($_POST['cnvID']);
}
if (!is_object($cn)) {
	$ve->add(t('Invalid conversation.'));
} else {
    $pp = new Permissions($cn);
    if (!$pp->canAddConversationMessage()) {
        $ve->add(t('You do not have access to add a message to this conversation.'));
    }

}

if(!is_object($pageObj)) {
	$ve->add(t('Invalid Page.'));
}

if(!is_object($blockObj)) {
	$ve->add(t('Invalid Page.'));
}

if(is_object($blockObj)) {
	if($_POST['attachments'] && count($_POST['attachments'])) {
        if (is_object($pp) && !$pp->canAddConversationMessageAttachments()) {
            $ve->add(t('You do not have permission to add attachments.'));
        } else {
            $maxFiles = $u->isRegistered() ? $blockObj->getController()->maxFilesRegistered : $blockObj->getController()->maxFilesGuest;
            if($maxFiles > 0 && count($_POST['attachments']) > $maxFiles) {
                $ve->add(t('You have too many attachments.'));
            }
        }
	}
}


if (!Loader::helper('validation/token')->validate('add_conversation_message', $_POST['token'])) {
	$ve->add(t('Invalid conversation post token.'));
}
if (!$vs->notempty($_POST['cnvMessageBody'])) {
	$ve->add(t('Your message cannot be empty.'));
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageParentID']) && $_POST['cnvMessageParentID'] > 0) {
	$parent = ConversationMessage::getByID($_POST['cnvMessageParentID']);
	if (!is_object($parent)) {
		$ve->add(t('Invalid parent message.'));
	}
}

if (Config::get('conversation.banned_words') && Loader::helper('validation/banned_words')->hasBannedWords($_POST['cnvMessageBody'])) {
	$ve->add(t('Banned words detected.'));
}


if ($ve->has()) {
	$ax->sendError($ve);
} else {
	$msg = ConversationMessage::add($cn, $cnvMessageSubject, $_POST['cnvMessageBody'], $parent);
	if (!Loader::helper('validation/antispam')->check($_POST['cnvMessageBody'],'conversation_comment')) {
		$msg->flag(ConversationFlagType::getByHandle('spam'));
	} else {
		$msg->approve();
	}
	if($_POST['attachments'] && count($_POST['attachments'])) {
		foreach($_POST['attachments'] as $attachmentID) {
            $msg->attachFile(File::getByID($attachmentID));
		}
	}
	$ax->sendResult($msg);
}
