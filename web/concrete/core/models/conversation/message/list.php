<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation_Message_List extends ItemList {

	protected $sortBy = 'date';
	protected $sortByDirection = 'asc';
	protected $cnvID;

	public function getMessages($orderBy = 'date_asc', $limit = false) {
		$this->populateMessages($orderBy);
		if (!$limit) {
			return $this->messages;
		}
		$messages = array_slice($this->messages, 0, $limit);
		return $messages;
	}

	public function __construct(Conversation $cnv) {
		$this->cnvID = $cnv->getConversationID();
		$this->populateMessages();
	}

	public function sortByDateDescending() {
		$this->sortBy('date', 'desc');
	}
	
	public function sortByDateAscending() {
		$this->sortBy('date', 'asc');
	}
	
	public function sortByRating() {
		$this->sortBy('date', 'asc');
	}
	
	public function get($num = 0, $offset = 0) {
		usort($this->items, array($this, 'sortItems'));
		return parent::get($num, $offset);
	}

	protected function populateMessages($cnvMessageParentID = 0) {
		$db = Loader::db();
		$v = array($this->cnvID, $cnvMessageParentID);
		$r = $db->Execute('select cnvMessageID from ConversationMessages where cnvID = ? and cnvMessageParentID = ?', $v);
		while ($row = $r->FetchRow()) {
			$msg = ConversationMessage::getByID($row['cnvMessageID']);
			if (is_object($msg)) {
				$this->items[] = $msg;
				$this->populateMessages($msg->getConversationMessageID());
			}
		}
	}

	protected function sortItems($a, $b) {
		if ($this->sortBy == 'date') {
			$atime = strtotime($a->getConversationMessageDateTime());
			$btime = strtotime($b->getConversationMessageDateTime());
			if ($this->sortByDirection == 'asc') {
				if ($atime > $btime) {
					return 1;
				} else if ($atime < $btime) {
					return -1;
				} else {
					return 0;
				}
			} else {
				if ($atime > $btime) {
					return -1;
				} else if ($atime < $btime) {
					return 1;
				} else {
					return 0;
				}
			}
		}
	}
}
