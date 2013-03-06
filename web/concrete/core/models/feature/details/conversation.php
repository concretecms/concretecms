<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ConversationFeatureDetail extends FeatureDetail {

	protected $cnvID;

	public function __construct($mixed) {
		$conversation = $mixed->getConversationFeatureDetailConversationObject();
		$this->cnvID = $conversation->getConversationID();
	}

	public function getConversationObject() {return Conversation::getByID($this->cnvID);}

}
