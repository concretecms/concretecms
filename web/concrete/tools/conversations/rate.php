<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Rating\Type as ConversationRatingType;

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {

	$ratingType = ConversationRatingType::getByHandle($_POST['cnvRatingTypeHandle']);
	$cnvMessageID = $_POST['cnvMessageID'];
	$commentRatingUserID = $_POST['commentRatingUserID'];
	$commentRatingIP = $_POST['commentRatingIP'];
	$msg = ConversationMessage::getByID($cnvMessageID);
    $msp = new Permissions($msg);
    if ($msp->canRateConversationMessage()) {
    	$msg->rateMessage($ratingType, $commentRatingIP, $commentRatingUserID);
    }
}