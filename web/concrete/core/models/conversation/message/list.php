<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation_Message_List extends DatabaseItemList {

	protected $sortBy = 'cnvMessageDateCreated';
	protected $sortByDirection = 'asc';
	protected $cnvID;

	public function __construct(Conversation $cnv) {
		$this->setQuery('select cnvMessageID from ConversationMessages');
		$this->filter('cnvID', $cnv->getConversationID());
	}

	public function sortByDateDescending() {
		$this->sortBy('cnvMessageDateCreated', 'desc');
	}
	
	public function sortByDateAscending() {
		$this->sortBy('cnvMessageDateCreated', 'asc');
	}
	
	public function get($num = 0, $offset = 0) {
		$r = parent::get($num, $offset);
		$messages = array();
		foreach($r as $row) {
			$messages[] = ConversationMessage::getByID($row['cnvMessageID']);	
		}
		return $messages;
	}

}
