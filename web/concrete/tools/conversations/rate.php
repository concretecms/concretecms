<?php defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {

	$ratingType = ConversationRatingType::getByHandle($_POST['cnvRatingTypeHandle']);
	$cnvMessageID = $_POST['cnvMessageID'];
	
	$msg = ConversationMessage::getByID($_POST[$cnvMessageID]);
	
	$msg->rateMessage($ratingType, $cnvMessageID);
	
}