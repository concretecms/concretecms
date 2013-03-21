<?php defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {

	$msg = ConversationMessage::getByID($_POST['cnvMessageID']);
	$ratingType = ConversationRatingType::getByHandle($_POST['cnvRatingTypeHandle']);

	$msg->rateMessage($ratingType, $_POST);

}
