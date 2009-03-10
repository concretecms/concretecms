<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('survey');

// Custom sorting function to sort by number of responses
function responseSort($a, $b) {
	if ($a['numberOfResponses'] == $b['numberOfResponses']) {
		return 0;
	}
	
	// Check the order we need to sort
	if ($_GET['dir'] == 'asc') {
		return ($a['numberOfResponses'] < $b['numberOfResponses']) ? -1 : 1;
	}
	else {
		return ($a['numberOfResponses'] < $b['numberOfResponses']) ? 1 : -1;
	}
}

class DashboardReportsSurveysController extends Controller {
	
	public function viewDetail($bID = 0) {
		// If a valid bID is set, get the corresponding data
		if ($bID > 0) {
			$this->getSurveyDetails($bID);
			$this->getChart($bID);
		} else { // Otherwise, redirect the page
			$this->redirect('/dashboard/reports/surveys');
		}
	}
	
	public function view() { 	
		// Prepare the database query
		$db = Loader::db();
		
		$sl = new SurveyList();
		$sl->view();
		$slResults = $sl->getPage();
		
		// Build array of information we need
		$surveys = array();
		
		foreach ($slResults as $row) {
			// Store bID, Name, and Found on Page
			$surveys[$row['bID']]['bID'] = $row['bID'];
			$surveys[$row['bID']]['name'] = $row['question'];
			$surveys[$row['bID']]['foundOnPage'] = $row['cvName'];
			
			// Get last response time
			$sl2 = new SurveyList();
			$sl2->getLastResponseTime($row['bID']);
			$slResults2 = $sl2->getPage();
			
			// Format last response time into a more readable format
			if ($row2 = $slResults2) {
				$surveys[$row['bID']]['lastResponse'] = $this->formatDate($row2[0]['max(timestamp)']);
			}
			else {
				$surveys[$row['bID']]['lastResponse'] = 'None';
			}
			
			// Reset variable (safeguard)
			$row2 = null;
			
			// Get number of responses
			$sl2->getNumberOfResponses($row['bID']);
			if ($row2 = $sl2->getPage()) {
				$surveys[$row['bID']]['numberOfResponses'] = $row2[0]['count(*)'];
			}
			else {
				$surveys[$row['bID']]['numberOfResponses'] = 0;
			}
		}
		
		// Reorganize array if the sort mode is "Number of Responses"
		if ($_GET['sortBy'] == 'numberOfResponses') {			
			usort($surveys, "responseSort");
		}
		
		// Store data in variable stored in larger scope
		$this->set('surveys', $surveys);	
	}	
	
	public function getSurveyDetails($bID) {
		$bID = intval($bID);
		
		// Prepare the database query
		$db = Loader::db();
		
		// Load the data from the database
		$sl = new SurveyList;
		$sl->getSurveyDetails($bID);
		$slResults = $sl->getPage();
		
		// Build array of information we need
		$details = array();
		$i = 0;
		foreach ($slResults as $row) {
			$details[$i]['option'] = $row['optionName'];
			$details[$i]['ipAddress'] = $row['ipAddress'];
			$details[$i]['date'] = $this->formatDate($row['timestamp']);
			$details[$i]['user'] = $row['uName'];
			$i++;
		}
		
		// Get the current survey question
		$sl = new SurveyList;
		$sl->getSurveyQuestion($bID);
		$slResults = $sl->getPage();
		if ($row = $slResults) {
			$current_curvey = $row[0]['question'];
		}
		else { // Dummy text if all else fails
			$current_curvey = 'Survey'; 
		}
		
		// Store local data in larger scope
		$this->set('survey_details', $details);
		$this->set('current_survey', $current_curvey);
	}
	
	private function getChart($bID) {
		// Prepare the database query
		$db = Loader::db();
		
		$bID = intval($bID);
		
		// Get all available options
		$options = array();
		$sl = new SurveyList;
		$sl->getSurveyOptions($bID);
		$slResults = $sl->getPage();
		$i = 0;
		foreach ($slResults as $row) {
			$options[$i]['name'] = $row['optionName'];
			$options[$i]['id'] = $row['optionID'];
			$options[$i]['amount'] = 0;	
			$i++;
		}
		
		// Get chosen count for each option
		$total_results = 0;
		$i = 0;
		foreach ($options as $option) {
			$sl = new SurveyList;
			$sl->getOptionChosenCount($option['id'], $bID);
			$slResults = $sl->getPage();
			if ($row = $slResults[0]) {
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
	
	private function formatDate($InputTime) {
		$timestamp = strtotime($InputTime);
		if ($timestamp) { // If today
			if ($timestamp >= strtotime(date('n/d/y'))) {
				return 'Today at ' . date('g:i a', $timestamp);
			}
			else { // If day in past
				return date('n/j/y \a\t g:i a', $timestamp);
			}
		}	
		return '';
	}
}

class SurveyList extends DatabaseItemList {
	protected $itemsPerPage = 100;
	
	function __construct() {
		// $this->debug();
	}
	
	public function view() {
		$this->setQuery(
			   'select ' .
					'btSurvey.bID, btSurvey.question, CollectionVersions.cvName ' .
				'from ' . 	
					'btSurvey, CollectionVersions, CollectionVersionBlocks');	
		$this->filter(false, 'btSurvey.bID = CollectionVersionBlocks.bID');
		$this->filter(false, 'CollectionVersions.cID = CollectionVersionBlocks.cID');
		$this->filter(false, 'CollectionVersionBlocks.cvID = CollectionVersionBlocks.cvID');
		$this->filter(false, 'CollectionVersions.cvIsApproved = 1');
		
		// Parse GET parameter so we're not passing a GET parameter directly into a query
		$direction = ($_GET['dir'] == 'asc') ? 'asc' : 'desc';
		
		switch ($_GET['sortBy']) {
			case 'name':
				$this->sortBy('btSurvey.question', $direction);
				break;
			// "Number of Responses" case handled back in invoking function
			default:
				$this->sortBy('btSurvey.bID', $direction);
				break;
		}
	}
	
	public function getLastResponseTime($bID) {
		$this->setQuery('select max(timestamp) from btSurveyResults');
		$this->filter('bID', intval($bID), '=');
	}
	
	public function getNumberOfResponses($bID) {
		$this->setQuery('select count(*) from btSurveyResults');
		$this->filter('bID', intval($bID), '=');	
	}
	
	public function getSurveyDetails($bID) {
		$this->setQuery(
		'select ' .
			'btSurveyOptions.optionName, ipAddress, timestamp, Users.uName ' .
		 'from ' .
			'btSurveyResults, Users, btSurveyOptions ');
		$this->filter(false, 'Users.uID = btSurveyResults.uID');
		$this->filter(false, 'btSurveyResults.optionID = btSurveyOptions.optionID');
		$this->filter(false, 'btSurveyResults.bID = ' . intval($bID));	
		$this->sortBy('timestamp', 'desc');
	}
	
	public function getSurveyQuestion($bID) {
		$this->setQuery('select question from btSurvey');
		$this->filter('bID', intval($bID), '=');	
	}
	
	public function getSurveyOptions($bID) {
		$this->setQuery(
			'select ' .
				'optionName, optionID ' . 
			'from ' . 
				'btSurveyOptions');
		$this->filter('bID', intval($bID), '=');
		$this->sortBy('displayOrder');
	}
	
	public function getOptionChosenCount($optionID, $bID) {
		$this->setQuery(
			'select '.
				'count(*) ' . 
			'from ' .
				'btSurveyResults ');
		$this->filter('optionID', intval($optionID), '=');
		$this->filter('bID', intval($bID), '=');
	}
}

?>