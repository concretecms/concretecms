<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_DownVoteConversationRatingType extends ConversationRatingType {

	public function outputRatingTypeHTML() {
		print '<i data-conversation-rating-type="down_vote" class="conversation-rate-message icon-thumbs-down"></i>';
	}


}