<?php
namespace Concrete\Core\Conversation\Message;
use Loader;
use \Concrete\Core\Legacy\ItemList;
use Conversation, ConversationMessage;
class ThreadedList extends ItemList {

    protected $sortBy = 'cnvMessageDateCreated';
    protected $sortByDirection = 'asc';
    protected $cnvID;

    public function __construct(Conversation $cnv) {
        $this->cnvID = $cnv->getConversationID();
        $this->messages = $this->getMessages();
        $this->total = $cnv->getConversationMessagesTotal();
    }

    public function sortByDateDescending() {
        $this->sortBy = 'date';
        $this->sortByDirection = 'desc';
    }

    public function sortByDateAscending() {
        $this->sortBy = 'date';
        $this->sortByDirection = 'asc';
    }

    public function sortByRating() {
        $this->sortBy = 'rating';
        $this->sortByDirection = 'desc';
    }

    public function get($num = 0, $offset = 0) {
        $messages = $this->sortThreadedArrays($this->messages);
        // now we turn the threaded messages array into the flat items array
        $this->flattenMessages($messages);
        return parent::get($num, $offset);
    }

    protected function sortThreadedArrays($messages) {
        usort($messages, array($this, 'sortItems'));
        foreach($messages as $m) {
            if (is_array($m->messages) && count($m->messages)) {
                $m->messages = $this->sortThreadedArrays($m->messages);
            }
        }
        return $messages;
    }

    protected function flattenMessages($messages) {
        foreach($messages as $m) {
            $this->items[] = $m;
            if (is_array($m->messages) && count($m->messages)) {
                $this->flattenMessages($m->messages);
            }
        }
    }

    public function getTotal() {
        return $this->total;
    }

    protected function getMessages($cnvMessageParentID = 0) {
        $db = Loader::db();
        $v = array($this->cnvID, $cnvMessageParentID);
        $r = $db->Execute('select cnvMessageID from ConversationMessages where cnvID = ? and cnvMessageParentID = ?', $v);
        $messages = array();
        while ($row = $r->FetchRow()) {
            $msg = ConversationMessage::getByID($row['cnvMessageID']);
            if (is_object($msg)) {
                $msg->messages = $this->getMessages($msg->getConversationMessageID());
                $messages[] = $msg;
            }
        }
        return $messages;
    }

    /**
     * @param ConversationMessage $a
     * @param ConversationMessage $b
     * @return int
     */
    protected function sortItems($a, $b) {

        $aSortVal = 0; //a sort value for a comparison check for the value of $a
        $bSortVal = 0; //a sort value for a comparison check for the value of $b

        if ($this->sortBy == 'date') {
            $aSortVal = strtotime($a->getConversationMessageDateTime());
            $bSortVal = strtotime($b->getConversationMessageDateTime());
        } elseif ($this->sortBy == 'rating') {
            $aSortVal = $a->getConversationMessagetotalRatingScore();
            $bSortVal = $b->getConversationMessagetotalRatingScore();
        }


        if ($this->sortByDirection == 'asc') {
            if ($aSortVal > $bSortVal) {
                return 1;
            } else if ($aSortVal < $bSortVal) {
                return -1;
            } else {
                return 0;
            }
        } else {
            if ($aSortVal > $bSortVal) {
                return -1;
            } else if ($aSortVal < $bSortVal) {
                return 1;
            } else {
                return 0;
            }
        }
    }
}
