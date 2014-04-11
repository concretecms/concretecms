<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Conversation\Rating\Type as ConversationRatingType;
use Conversation, ConversationMessage;


if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {


	$ratingType = ConversationRatingType::getByHandle($_POST['cnvRatingTypeHandle']);
	$cnvMessageID = $_POST['cnvMessageID'];
	$commentRatingUserID = $_POST['commentRatingUserID'];
	$commentRatingIP = $_POST['commentRatingIP'];
	$msg = ConversationMessage::getByID($cnvMessageID);
	$msg->rateMessage($ratingType, $commentRatingIP, $commentRatingUserID);
}