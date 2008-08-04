<?
class SurveyBlockController extends BlockController {
	/** 
	* @var object
	*/
	var $pobj;
	
	protected $btDescription = "Survey block";
	protected $btName = "Survey";
	protected $btTable = 'btSurvey';
	protected $btInterfaceWidth = "420";
	protected $btInterfaceHeight = "300";	
	protected $btIncludeAll = 1;
	
	var $options = array();
	
	
	function __construct($obj = NULL) {
		parent::__construct($obj);
		
		if($this->bID) {
			$db = Loader::db();
			$v = array($this->bID);
			$q = "select optionID, optionName, displayOrder from btSurveyOptions where bID = ? order by displayOrder asc";
			$r = $db->query($q, $v);
			$this->options = array();
			if ($r) {
				while ($row = $r->fetchRow()) {
					$opt = new BlockPollOption;
					$opt->optionID = $row['optionID'];
					$opt->optionName = $row['optionName'];
					$opt->displayOrder = $row['displayOrder'];
					$this->options[] = $opt;
				}
			}
		}
	}
	
	function getQuestion() {return $this->question;}
	function getPollOptions() { return $this->options; }
	function requiresRegistration() {return $this->requiresRegistration;}
	
	function hasVoted() {
		$u = new User();
		if ($u->isRegistered()) {
			$db = Loader::db();
			$v = array($u->getUserID(), $this->bID);
			$q = "select count(resultID) as total from btSurveyResults where uID = ? and bID = ?";
			$result = $db->getOne($q,$v);
			if ($result > 0) {
				return true;
			}
		} elseif ($_COOKIE['ccmPoll' . $this->bID] == 'voted') {
			return true;
		}
		return false;
	}
	
	function delete() {
		$db = Loader::db();
		$v = array($this->bID);
		
		$q = "delete from btSurveyOptions where bID = ?";
		$db->query($q, $v);
		
		$q = "delete from btSurveyResults where bID = ?";
		$db->query($q, $v);
		
		return parent::delete();
	}
	
	function action_form_save_vote() {
		$u = new User();
		$db = Loader::db();
		$c = $this->pobj->getBlockCollectionObject();
		if ($this->requiresRegistration()) {
			if (!$u->isRegistered()) {
				$this->redirect('/login');
			}			
		}
		
		if (!$this->hasVoted()) {
			$duID = 0;
			if($u->getUserID()>0) {
				$duID = $u->getUserID();
			}
			
			$v = array($_REQUEST['optionID'], $this->bID, $duID, $_SERVER['REMOTE_ADDR']);
			$q = "insert into btSurveyResults (optionID, bID, uID, ipAddress) values (?, ?, ?, ?)";
			$db->query($q, $v);
			setcookie("ccmPoll" . $this->bID, "voted", time() + 1296000, DIR_REL . '/');
			$this->redirect($c->getCollectionPath());
		}
	}		

	function duplicate($newBID) {
		
		$db = Loader::db();
		
		foreach($this->options as $opt) {
			$v1 = array($newBID, $opt->getOptionName(), $opt->getOptionDisplayOrder());
			$q1 = "insert into btSurveyOptions (bID, optionName, displayOrder) values (?, ?, ?)";
			$db->query($q1, $v1);
			
			$v2 = array($opt->getOptionID());
			$newOptionID = get_insert_id();
			$q2 = "select * from btSurveyResults where optionID = ?";
			$r2 = $db->query($q2, $v2);
			if ($r2) {
				while ($row = $r2->fetchRow()) {
					$v3 = array($newOptionID, $row['uID'], $row['ipAddress'], $row['timestamp']);
					$q3 = "insert into btSurveyResults (optionID, uID, ipAddress, timestamp) values (?, ?, ?, ?)";
					$db->query($q3, $v3);
				}
			}
		}
		
		return parent::duplicate($newBID);
		
	}
	
	function save($args) {
		parent::save($args);
		$db = Loader::db();
		
		if(!is_array($args['survivingOptionNames'])) {
			$args['survivingOptionNames'] = array();
		}
		$db->query("DELETE FROM btSurveyOptions WHERE optionName NOT IN ('".implode("','",$args['survivingOptionNames'])."') AND bID = ?",array($this->bID));
			
		if (is_array($args['pollOption'])) {
			$displayOrder = 0;
			foreach($args['pollOption'] as $optionName) {
				$v1 = array($this->bID, $optionName, $displayOrder);	
				$q1 = "insert into btSurveyOptions (bID, optionName, displayOrder) values (?, ?, ?)";
				$db->query($q1, $v1);
				$displayOrder++;
			}
		}
		
		$query = "DELETE FROM btSurveyResults 
			WHERE optionID NOT IN (
				SELECT optionID FROM btSurveyOptions WHERE bID = {$this->bID}
				) 
			AND bID = {$this->bID} ";
		$db->query($query);
	}	
}




class BlockPollOption {
	var $optionID, $optionName, $displayOrder;
	
	function getOptionID() {return $this->optionID;}
	function getOptionName() {return $this->optionName;}
	function getOptionDisplayOrder() {return $this->displayOrder;}
	
	function getResults() {
		$db = Loader::db();
		$v = array($this->optionID);
		$q = "select count(resultID) from btSurveyResults where optionID = ?";
		$result = $db->getOne($q, $v);
		if ($result > 0) {
			return $result;
		} else {
			return 0;
		}
	}
}


?>