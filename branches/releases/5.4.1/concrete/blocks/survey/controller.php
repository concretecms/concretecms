<?php 
/**
 * @package Blocks
 * @subpackage BlockTypes
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * Controller for the survey block, which allows site owners to add surveys and uses Google's graphing web service to display results.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

defined('C5_EXECUTE') or die("Access Denied.");
class SurveyBlockController extends BlockController {
	 
	protected $btTable = 'btSurvey';
	protected $btInterfaceWidth = "420";
	protected $btInterfaceHeight = "300";	
	protected $btIncludeAll = 1;
	
	var $options = array();

	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Provide a simple survey, along with results in a pie chart format.");
	}
	
	public function getBlockTypeName() {
		return t("Survey");
	} 
	
	function __construct($obj = NULL) {
		parent::__construct($obj);
		$c = $this->getCollectionObject();
		
		if (is_object($c)) {
			$this->cID = $c->getCollectionID();
		}
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
					$opt->cID = $this->cID;
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
			$v = array($u->getUserID(), $this->bID, $this->cID);
			$q = "select count(resultID) as total from btSurveyResults where uID = ? and bID = ? AND cID = ?";
			$result = $db->getOne($q,$v);
			if ($result > 0) {
				return true;
			}
		} elseif ($_COOKIE['ccmPoll' . $this->bID.'-'.$this->cID] == 'voted') {
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
		$bo = $this->getBlockObject();
		$c = $this->getCollectionObject();
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
			
			$v = array($_REQUEST['optionID'], $this->bID, $duID, $_SERVER['REMOTE_ADDR'], $this->cID);
			$q = "insert into btSurveyResults (optionID, bID, uID, ipAddress, cID) values (?, ?, ?, ?, ?)";
			$db->query($q, $v);
			setcookie("ccmPoll" . $this->bID.'-'.$this->cID, "voted", time() + 1296000, DIR_REL . '/');
			$this->redirect($c->getCollectionPath() . '?survey_voted=1');
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
		
		if(!is_array($args['survivingOptionNames'])) 
			$args['survivingOptionNames'] = array();
 
		$slashedArgs=array();
		foreach($args['survivingOptionNames'] as $arg)
			$slashedArgs[]=addslashes($arg); 
		$db->query("DELETE FROM btSurveyOptions WHERE optionName NOT IN ('".implode("','",$slashedArgs)."') AND bID = ".intval($this->bID) );
			
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
	
	public function displayChart($bID, $cID) {
		// Prepare the database query
		$db = Loader::db();
		
		// Get all available options
		$options = array();
		$v = array(intval($bID));
		$q = 'select optionID, optionName from btSurveyOptions where bID = ? order by displayOrder asc';
		$r = $db->Execute($q, $v);
		
		$i = 0;
		while ($row = $r->fetchRow()) {
			$options[$i]['name'] = $row['optionName'];
			$options[$i]['id'] = $row['optionID'];
			$i++;
		}
		
		// Get chosen count for each option
		$total_results = 0;
		$i = 0;
		foreach ($options as $option) {
			$v = array($option['id'], intval($bID), intval($cID));
			$q = 'select count(*) from btSurveyResults where optionID = ? and bID = ? and cID = ?';
			$r = $db->Execute($q, $v);
			
			if ($row = $r->fetchRow()) {
				$options[$i]['amount'] = $row['count(*)'];
				$total_results += $row['count(*)'];
			}
			$i++;
		}
		
		if ($total_results <= 0) { 
			$chart_options = '<div style="text-align: center; margin-top: 15px;">No data is available yet.</div>';
			$this->set('chart_options', $chart_options);
			return;
		}		
		
		// Convert option counts to percentages, initiate colors
		$availableChartColors=array('00CCdd','cc3333','330099','FF6600','9966FF','dd7700','66DD00','6699FF','FFFF33','FFCC33','00CCdd','cc3333','330099','FF6600','9966FF','dd7700','66DD00','6699FF','FFFF33','FFCC33');
		$percentage_value_string = '';
		foreach ($options as $option) {
			$option['amount'] /= $total_results;
			$percentage_value_string .= round($option['amount'], 3) . ',';
			$graphColors[]=array_pop($availableChartColors);
		}
		
		// Strip off trailing comma
		$percentage_value_string = substr_replace($percentage_value_string,'',-1);
		
		// Get Google Charts API image
		$img_src = '<img border="" src="http://chart.apis.google.com/chart?cht=p&chd=t:' . $percentage_value_string . '&chs=120x120&chco=' . join(',',$graphColors) . '" />';
		$this->set('pie_chart', $img_src);
		
		// Build human-readable option list
		$i = 1;
		$chart_options = '<table style="margin-left: 20px; float: left; width: 130px;">';
		foreach($options as $option) {
			$chart_options .= '<tr>'; 
			$chart_options .= '<td width="55px" class="note" style="white-space:nowrap">';
			$chart_options .= '<div class="surveySwatch" style="background:#' . $graphColors[$i - 1] . '"></div>';
			$chart_options .= '&nbsp;' . ($option['amount'] > 0) ? round($option['amount'] / $total_results * 100) : 0;
			$chart_options .= '%</td>';
			$chart_options .= '<td>'; 
			$chart_options .= '<strong>' . $options[$i - 1]['name'] . '</strong>';
			$chart_options .= '</td>';
			$chart_options .= '</tr>';
			$i++;
		}
		$chart_options .= '</table>';
		$this->set('chart_options', $chart_options);
	}
}



/**
 * @package Blocks
 * @subpackage BlockTypes
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents a survey option.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class BlockPollOption {
	var $optionID, $optionName, $displayOrder;
	
	function getOptionID() {return $this->optionID;}
	function getOptionName() {return $this->optionName;}
	function getOptionDisplayOrder() {return $this->displayOrder;}
	
	function getResults() {
		$db = Loader::db();
		$v = array($this->optionID, intval($this->cID));
		$q = "select count(resultID) from btSurveyResults where optionID = ? AND cID=?";
		$result = $db->getOne($q, $v);
		if ($result > 0) {
			return $result;
		} else {
			return 0;
		}
	}
}


?>