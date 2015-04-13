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

$pk = PermissionKey::getByHandle('add_conversation_message');

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
	if (!$pk->validate()) {
        $ve->add(t('You do not have access to add a message to this conversation.'));
    } else {
		// We know that we have access. So let's check to see if the user is logged in. If they're not we're going
		// to validate their name and email.
		$author = new \Concrete\Core\Conversation\Message\Author();
		if (!$u->isRegistered()) {
			if (!$vs->notempty($_POST['cnvMessageAuthorName'])) {
				$ve->add(t('You must enter your name to post this message.'));
			} else {
				$author->setName($_POST['cnvMessageAuthorName']);
			}
			if (!$vs->email($_POST['cnvMessageAuthorEmail'])) {
				$ve->add(t('You must enter a valid email address to post this message.'));
			} else {
				$author->setEmail($_POST['cnvMessageAuthorEmail']);
			}
            $author->setWebsite($_POST['cnvMessageAuthorWebsite']);

			$captcha = Core::make('captcha');
			if (!$captcha->check()) {
				$ve->add(t("Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary."));
			}
		} else {
			$author->setUser($u);
		}
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
	$msg = ConversationMessage::add($cn, $author, $cnvMessageSubject, $_POST['cnvMessageBody'], $parent);
	if (!Loader::helper('validation/antispam')->check($_POST['cnvMessageBody'],'conversation_comment')) {
		$msg->flag(ConversationFlagType::getByHandle('spam'));
	} else {
		$assignment = $pk->getMyAssignment();
		if ($assignment->approveNewConversationMessages()) {
			$msg->approve();
		}
	}
	if($_POST['attachments'] && count($_POST['attachments'])) {
		foreach($_POST['attachments'] as $attachmentID) {
            $msg->attachFile(File::getByID($attachmentID));
		}
	}
	$ax->sendResult($msg);
}
