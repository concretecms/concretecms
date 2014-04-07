<?php
namespace Concrete\Core\Conversation\Rating;
use Loader;
class DownVoteType extends Type {

	public function outputRatingTypeHTML() {
		print '<div class="conversation-rate-message-container"><i class="icon-thumbs-down conversation-rate-message" data-conversation-rating-type="down_vote"></i></div>';
	}
	
	public function rateMessage() {
	}

	public function adjustConversationMessageRatingTotalScore(ConversationMessage $message) {
		$db = Loader::db();
		$db->Execute('update ConversationMessages set cnvMessageTotalRatingScore = cnvMessageTotalRatingScore - 1 where cnvMessageID = ?', array(
			$message->getConversationMessageID()
		));
	}
}