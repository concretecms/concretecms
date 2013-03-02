<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ConversationFeature extends Feature {

	public function getFeatureDetailObject($mixed) {
		$fd = new ConversationFeatureDetail();
		$conversation = $mixed->getConversationObject();
		$fd->setConversationID($conversation->getConversationID());
		return $fd;
	}

}
