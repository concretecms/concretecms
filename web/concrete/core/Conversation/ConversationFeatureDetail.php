<?
namespace Concrete\Core\Conversation;
use \Concrete\Core\Feature\Detail as FeatureDetail;
class ConversationFeatureDetail extends FeatureDetail {

	protected $cnvID;

	public function __construct($mixed) {
		$conversation = $mixed->getConversationFeatureDetailConversationObject();
		$this->cnvID = $conversation->getConversationID();
	}

	public function getConversationObject() {return Conversation::getByID($this->cnvID);}

	public function handleFeatureAssignment(FeatureAssignment $fa) {
		// we ALSO write the information into a table. 
		// Yes, this is duplication of data but we need to join in the DB at times
		$db = Loader::db();
		$db->Execute('insert into ConversationFeatureDetailAssignments (faID, cnvID) values (?, ?)', array(
			$fa->getFeatureAssignmentID(), $this->cnvID
		));
	}

}
