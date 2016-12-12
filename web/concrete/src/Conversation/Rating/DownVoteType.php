<?php
namespace Concrete\Core\Conversation\Rating;
use Concrete\Core\Conversation\Message\Message;
use Loader;
class DownVoteType extends Type {

	public function outputRatingTypeHTML() {
        print '<a href="javascript:void(0)" class="conversation-rate-message ccm-conversation-message-control-icon" data-conversation-rating-type="down_vote"><i class="fa fa-thumbs-down"></i></a>';
	}
	
	public function rateMessage() {
	}

	public function adjustConversationMessageRatingTotalScore(Message $message) {
		$db = Loader::db();
		$db->Execute('update ConversationMessages set cnvMessageTotalRatingScore = cnvMessageTotalRatingScore - 1 where cnvMessageID = ?', array(
			$message->getConversationMessageID()
		));
	}
}