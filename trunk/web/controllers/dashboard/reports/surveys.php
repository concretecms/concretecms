<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('survey');

class DashboardReportsSurveysController extends Controller {
	
	public function viewDetail($bID = 0, $cID = 0) {
		// If a valid bID and cID are set, get the corresponding data
		if ($bID > 0 && $cID > 0) {
			$this->getSurveyDetails($bID, $cID);
			$this->getChart($bID, $cID);
		} else { // Otherwise, redirect the page to overview
			$this->redirect('/dashboard/reports/surveys');
		}
	}
	
	public function view() { 	
		// Prepare the database query
		$db = Loader::db();
		
		$sl = new SurveyList();
		$slResults = $sl->getPage();
		
		// Store data in variable stored in larger scope
		$this->set('surveys', $slResults);	
		$this->set('surveyList', $sl);
	}	
	
	public function getSurveyDetails($bID, $cID) {
		$bID = intval($bID);
		
		// Prepare the database query
		$db = Loader::db();
		
		// Load the data from the database
		$sl = new SurveyList;
		$sl->getSurveyDetails($bID, $cID);
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
		$sl->getSurveyQuestion($bID, $cID);
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
	
	private function getChart($bID, $cID) {
		// Prepare the database query
		$db = Loader::db();
		
		$bID = intval($bID);
		$cID = intval($cID);
		
		// Get all available options
		$options = array();
		$sl = new SurveyList;
		$sl->getSurveyOptions($bID, $cID);
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
			$sl->getOptionChosenCount($option['id'], $bID, $cID);
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
	protected $itemsPerPage = 10;
	protected $autoSortColumns = array('cvName', 'question', 'numberOfResponses', 'lastResponse');
	function __construct() {

		$this->setQuery(
			   'select distinct btSurvey.bID, CollectionVersions.cID, btSurvey.question, CollectionVersions.cvName, (select max(timestamp) from btSurveyResults where btSurveyResults.bID = btSurvey.bID and btSurveyResults.cID = CollectionVersions.cID) as lastResponse, (select count(timestamp) from btSurveyResults where btSurveyResults.bID = btSurvey.bID and btSurveyResults.cID = CollectionVersions.cID) as numberOfResponses ' .
				'from btSurvey, CollectionVersions, CollectionVersionBlocks');	
		$this->filter(false, 'btSurvey.bID = CollectionVersionBlocks.bID');
		$this->filter(false, 'CollectionVersions.cID = CollectionVersionBlocks.cID');
		$this->filter(false, 'CollectionVersionBlocks.cvID = CollectionVersionBlocks.cvID');
		$this->filter(false, 'CollectionVersions.cvIsApproved = 1');
		$this->userPostQuery .= 'group by btSurvey.bID, CollectionVersions.cID';
	}
	
	public function getLastResponseTime($bID, $cID) {
		$this->setQuery('select max(timestamp) from btSurveyResults, btSurvey, CollectionVersions, CollectionVersionBlocks');
		$this->filter('btSurveyResults.bID', intval($bID), '=');
		$this->filter('CollectionVersionBlocks.cID', intval($cID), '=');
	}
	
	public function getNumberOfResponses($bID, $cID) {
		$this->setQuery('select count(*) from btSurveyResults, btSurvey, CollectionVersions, CollectionVersionBlocks');
		$this->filter('btSurveyResults.bID', intval($bID), '=');	
		$this->filter('CollectionVersionBlocks.cID', intval($cID), '=');
	}
	
	public function getSurveyDetails($bID, $cID) {
		$this->setQuery(
		'select ' .
			'btSurveyOptions.optionName, ipAddress, timestamp, Users.uName ' .
		 'from ' .
			'btSurveyResults, Users, btSurveyOptions, btSurvey, CollectionVersions, CollectionVersionBlocks');
		$this->filter(false, 'Users.uID = btSurveyResults.uID');
		$this->filter(false, 'btSurveyResults.optionID = btSurveyOptions.optionID');
		$this->filter('btSurveyResults.bID', intval($bID), '=');	
		$this->filter('CollectionVersionBlocks.cID', intval($cID), '=');
		$this->sortBy('timestamp', 'desc');
	}
	
	public function getSurveyQuestion($bID, $cID) {
		$this->setQuery('select question from btSurvey, CollectionVersions, CollectionVersionBlocks');
		$this->filter('btSurvey.bID', intval($bID), '=');
		$this->filter('CollectionVersionBlocks.cID', intval($cID), '=');	
	}
	
	public function getSurveyOptions($bID, $cID) {
		$this->setQuery(
			'select ' .
				'optionName, optionID ' . 
			'from ' . 
				'btSurveyOptions, btSurvey, CollectionVersions, CollectionVersionBlocks');
		$this->filter('btSurveyOptions.bID', intval($bID), '=');
		$this->filter('CollectionVersionBlocks.cID', intval($cID), '=');
		$this->sortBy('displayOrder');
	}
	
	public function getOptionChosenCount($optionID, $bID, $cID) {
		$this->setQuery(
			'select '.
				'count(*) ' . 
			'from ' .
				'btSurveyResults, btSurvey, CollectionVersions, CollectionVersionBlocks');
		$this->filter('btSurveyResults.optionID', intval($optionID), '=');
		$this->filter('btSurveyResults.bID', intval($bID), '=');
		$this->filter('CollectionVersionBlocks.cID', intval($cID), '=');
	}
}

?>