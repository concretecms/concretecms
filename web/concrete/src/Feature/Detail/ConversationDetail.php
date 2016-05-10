<?php
namespace Concrete\Core\Feature\Detail;

use Concrete\Core\Feature\Assignment\Assignment;
use Concrete\Core\Conversation\Conversation;
use Database;

class ConversationDetail extends Detail
{
    protected $cnvID;

    public function __construct($mixed)
    {
        $conversation = $mixed->getConversationFeatureDetailConversationObject();
        $this->cnvID = $conversation->getConversationID();
    }

    public function getConversationObject()
    {
        return Conversation::getByID($this->cnvID);
    }

    public function handleFeatureAssignment(Assignment $fa)
    {
        // we ALSO write the information into a table.
        // Yes, this is duplication of data but we need to join in the DB at times
        $db = Database::get();
        $db->Execute('insert into ConversationFeatureDetailAssignments (faID, cnvID) values (?, ?)', array(
            $fa->getFeatureAssignmentID(),
            $this->cnvID,
        ));
    }

    public function deleteFeatureAssignment(Assignment $fa)
    {
        $db = Database::get();
        $db->delete('ConversationFeatureDetailAssignments', array('faID' => $fa->getFeatureAssignmentID()));
    }
}
