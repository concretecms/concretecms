<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ConversationFeatureType extends FeatureType {

	public function getFeatureDetailObject($mixed) {
		$fd = new ConversationFeatureDetail();
		$conversation = $mixed->getConversationObject();
		$fd->setConversationID($conversation->getConversationID());
		return $fd;
	}

}
