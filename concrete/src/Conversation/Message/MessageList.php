<?php
namespace Concrete\Core\Conversation\Message;
use Loader;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\FlagType\FlagType;
use \Concrete\Core\Legacy\DatabaseItemList;
class MessageList extends DatabaseItemList {
    protected $autoSortColumns = array('cnvMessageDateCreated');
	protected $sortBy = 'cnvMessageDateCreated';
	protected $sortByDirection = 'asc';
	protected $cnvID;

	public function __construct() {
		$this->setQuery('select cnvm.cnvMessageID from ConversationMessages cnvm');
	}

	public function filterByConversation(Conversation $cnv) {
		$this->filter('cnvID', $cnv->getConversationID());
	}

	public function sortByDateDescending() {
		$this->sortBy('cnvMessageDateCreated', 'desc');
	}

	public function filterByFlag(FlagType $type) {
		$this->addToQuery('inner join ConversationFlaggedMessages cnf on cnvm.cnvMessageID = cnf.cnvMessageID');
		$this->filter('cnf.cnvMessageFlagTypeID', $type->getConversationFlagTypeID());
	}
	
	public function sortByDateAscending() {
		$this->sortBy('cnvMessageDateCreated', 'asc');
	}

    public function sortByRating() {
        $this->sortBy('cnvMessageTotalRatingScore', 'desc');
    }

	public function filterByApproved() {
		$this->filter('cnvIsMessageApproved', 1);
	}

	public function filterByNotDeleted() {
		$this->filter('cnvIsMessageDeleted', 0);
	}

	public function filterByUnapproved() {
		$this->filter('cnvIsMessageApproved', 0);
	}
	
	public function filterByUser($uID) {
		$this->filter('uID', $uID);
	}
	
	public function filterByDeleted() {
		$this->filter('cnvIsMessageDeleted', 1);
	}

	public function filterByKeywords($keywords) {
		$this->addToQuery('inner join Conversations cnv on cnvm.cnvID = cnv.cnvID left join CollectionVersions cv on (cnv.cID = cv.cID and cv.cvIsApproved = 1)');

		$db = Loader::db();
		$qk = $db->quote('%' . $keywords . '%');
		$this->filter(false, "(cnvMessageSubject like $qk or cnvMessageBody like $qk or cvName like $qk)");		
	}
	
	public function get($num = 0, $offset = 0) {
		$r = parent::get($num, $offset);
		$messages = array();
		foreach($r as $row) {
			$messages[] = Message::getByID($row['cnvMessageID']);	
		}
		return $messages;
	}

}
