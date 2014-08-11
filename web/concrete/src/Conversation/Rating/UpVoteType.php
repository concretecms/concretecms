<?php
namespace Concrete\Core\Conversation\Rating;
use \Concrete\Core\Foundation\Object;
use Loader;
class UpVoteType extends Type {

	public function outputRatingTypeHTML() {
		print '<a href="javascript:void(0)" class="ccm-conversation-message-control-icon" data-conversation-rating-type="up_vote"><i class="fa fa-thumbs-up conversation-rate-message"></i></a>';
	}
	
	public function rateMessage() {
		
	}

	public function adjustConversationMessageRatingTotalScore(ConversationMessage $message) {
		$db = Loader::db();
		$db->Execute('update ConversationMessages set cnvMessageTotalRatingScore = cnvMessageTotalRatingScore + 1 where cnvMessageID = ?', array(
			$message->getConversationMessageID()
		));
	}

}