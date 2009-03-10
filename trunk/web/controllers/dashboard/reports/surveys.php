<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('survey');

/* TODO
	- Run through code again, make sure everything is documented properly
	- Make sure everything is optimized
	- Make a JQuery-callable function to laod survey details
*/

class DashboardReportsSurveysController extends Controller {
	
	public function view_detail($bID = 0) {
		if ($bID > 0) {
			$this->get_survey_details($bID);
			$this->get_chart($bID);
		} else {
			$this->redirect('/dashboard/reports/surveys');
		}
	}
	
	public function view() { 	
		// Prepare the database query
		$db = Loader::db();
		
		$q1 = 'select btSurvey.bID, btSurvey.question, CollectionVersions.cvName

				from btSurvey, CollectionVersions, CollectionVersionBlocks

				where btSurvey.bID = CollectionVersionBlocks.bID 

				AND CollectionVersions.cID = CollectionVersionBlocks.cID and 

				CollectionVersionBlocks.cvID = CollectionVersionBlocks.cvID and CollectionVersions.cvIsApproved = 1';
		$r1 = $db->query($q1);
		
		// Build array of information we need
		$surveys = array();
		
		while ($row = $r1->fetchRow()) {
			// Store bID, Name, and Found on Page
			$surveys[$row['bID']]['bID'] = $row['bID'];
			$surveys[$row['bID']]['name'] = $row['question'];
			$surveys[$row['bID']]['foundOnPage'] = $row['cvName'];
			
			// Get last response time
			$q2 = 'select max(timestamp) from btSurveyResults where bID = ' . $row['bID'];
			$r2 = $db->query($q2);
			if ($row2 = $r2->fetchRow()) {
				$surveys[$row['bID']]['lastResponse'] = $row2['max(timestamp)'];
			}
			else {
				$surveys[$row['bID']]['lastResponse'] = 'None';
			}
			
			// Format last response time into a more readable format
			$surveys[$row['bID']]['lastResponse'] = $this->format_date($surveys[$row['bID']]['lastResponse']);
		
			// Reset variable (safeguard)
			$row2 = null;
			
			// Get number of responses
			$q2 = 'select count(*) from btSurveyResults where bID = ' . $row['bID'];
			$r2 = $db->query($q2);
			if ($row2 = $r2->fetchRow()) {
				$surveys[$row['bID']]['numberOfResponses'] = $row2['count(*)'];
			}
			else {
				$surveys[$row['bID']]['numberOfResponses'] = 0;
			}
		}
		
		// Get details for first entry
		
		// Store data in variable stored in larger scope
		$this->set('surveys', $surveys);	
	}	
	
	public function get_survey_details($bID) {
		// Prepare the database query
		$db = Loader::db();
		
		// Load the data from the database
		$q = 

		'select ' . 

			'btSurveyOptions.optionName, ipAddress, timestamp, Users.uName ' .

		 'from ' .

			'btSurveyResults, Users, btSurveyOptions ' . 

		 'where ' . 

			'Users.uID = btSurveyResults.uID and ' .

			'btSurveyResults.optionID = btSurveyOptions.optionID and ' . 

			'btSurveyResults.bID = ' . intval($bID);
		$r = $db->query($q);
		
		// Build array of information we need
		$details = array();
		$i = 0;
		while ($row = $r->fetchRow()) {
			$details[$i]['option'] = $row['optionName'];
			$details[$i]['ipAddress'] = $row['ipAddress'];
			$details[$i]['date'] = $this->format_date($row['timestamp']);
			$details[$i]['user'] = $row['uName'];
			$i++;	
		}
		
		// Store data in variable stored in larger scope
		$this->set('survey_details', $details);
	}
	
	private function get_chart($bID) {
		// Prepare the database query
		$db = Loader::db();
		
		$bID = intval($bID);
		
		// Get all available options
		$options = array();
		$i = 0;
		$q = 'select optionName, optionID from btSurveyOptions where bID = ' . 
			   $bID . ' order by displayOrder asc';
		$r = $db->query($q);
		while ($row = $r->fetchRow()) {
			$options[$i]['name'] = $row['optionName'];
			$options[$i]['id'] = $row['optionID'];
			$options[$i]['amount'] = 0;
			$i++;	
		}
		
		// Get chosen count for each option
		$total_results = 0;
		$i = 0;
		foreach ($options as $option) {
			$q = 'select count(*) from btSurveyResults where optionID = ' . 
			$option['id'] . ' and bID = ' . $bID;
			$r = $db->query($q);
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
			$percentage_value_string .= $option['amount'] . ',';
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
	
	private function format_date($InputTime) {
		$timestamp = strtotime($InputTime);
		if ($timestamp) { // If today
			if ($timestamp >= strtotime(date('n/d/y'))) {
				return 'Today at ' . date('g:i a', $timestamp);
			}
			else { // If day in past
				return date('n/d/y \a\t g:i a', $timestamp);
			}
		}	
		return '';
	}
}

?>