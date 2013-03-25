<?php defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
	
	$cnvMessageID = $_POST['cnvMessageID'];
	$msg = ConversationMessage::getByID($_POST[$cnvMessageID]);
	$ratingType = ConversationRatingType::getByHandle($_POST['cnvRatingTypeHandle']);
	
	
	$msg->rateMessage($ratingType, $cnvMessageID);

}

public function getRating(cnvMessageID) {
	$count = $db->Execute('SELECT COUNT FROM ConversationMessageRatings where cnvRatingTypeID = ? AND cnvMessageID = ? ', array ($cnvRatingTypeID, $cnvMessageID));
	return $count;
}