<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	$message = ConversationMessage::getByID($_POST['cnvMessageID']);
	if (is_object($message)) {
        $mp = new Permissions($message);
        if ($mp->canDeleteConversationMessage()) {
    		$message->delete();

			$r = Request::getInstance();
			$types = $r->getAcceptableContentTypes();
			if ($types[0] == 'application/json') {
				$r = new \Concrete\Core\Application\EditResponse();
				$r->setMessage(t('Message deleted successfully.'));
				$r->outputJSON();
			} else {
				Loader::element('conversation/message', array('message' => $message));
			}

	    }
    }
}
