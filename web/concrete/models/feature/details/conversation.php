<?
defined('C5_EXECUTE') or die("Access Denied.");
class ConversationFeatureDetail extends Concrete5_Model_ConversationFeatureDetail {

	public function setConversationID($cnvID) {
		$this->cnvID;
	}

	public function getConversationID() {
		return $this->cnvID;
	}

	public function getConversationObject() {
		return Conversation::getByID($this->cnvID);
	}
	
}