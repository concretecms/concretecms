<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ConversationFeatureDetail extends FeatureDetail {

	protected $cnvID;

	public function setConversationID($cnvID) {
		$this->cnvID = $cnvID;
	}

	public function getConversationID() {
		return $this->cnvID;
	}

	public function getConversationObject() {
		return Conversation::getByID($this->cnvID);
	}
	
	public static function get($mixed) {
		$fd = new ConversationFeatureDetail();
		$conversation = $mixed->getConversationObject();
		$fd->setConversationID($conversation->getConversationID());
		return $fd;
	}



}
