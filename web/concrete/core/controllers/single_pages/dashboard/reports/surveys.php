<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Reports_Surveys extends Controller {

	public function formatDate($inputTime) {
		if(empty($inputTime)) {
			return '';
		}
		$dh = Loader::helper('date');
		$inputTime = $dh->getLocalDateTime($inputTime);
		if (defined('DATE_APP_SURVEY_RESULTS')) {
			return $dh->date(DATE_APP_SURVEY_RESULTS, strtotime($inputTime));
		}
		$timestamp = strtotime($inputTime);
		if ($timestamp >= strtotime(date('n/d/y'))) {
			// Today
			return t(/*i18n %s is a time */'Today at %s', $dh->date(DATE_APP_GENERIC_T, $timestamp));
		}
		else {
			// If day in past
			return $dh->date(DATE_APP_GENERIC_MDYT, $timestamp);
		}
	}

	public function viewDetail($bID = 0, $cID = 0) {
		// If a valid bID and cID are set, get the corresponding data
		if ($bID > 0 && $cID > 0) {
			$this->getSurveyDetails($bID, $cID);
			SurveyBlockController::displayChart($bID, $cID);
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
		// Load the data from the database
		$db = Loader::db();
		$v = array(intval($bID), intval($cID));
		$q = 
			'SELECT 
				btSurveyOptions.optionName, Users.uName, ipAddress, timestamp, question 
			FROM
				(
						(
							btSurvey
							inner join btSurveyResults on btSurvey.bID= btSurveyResults.bID
						)
					inner join btSurveyOptions on btSurveyResults.optionID = btSurveyOptions.optionID
				)
				left join Users on btSurveyResults.uID = Users.uID
			WHERE
				btSurveyResults.bID = ?
				AND
				btSurveyResults.cID = ?';
		$r = $db->query($q, $v);
		
		// Set default information in case query returns nothing
		$current_survey = 'Unknown Survey';
		$details = array();
		
		if ($row = $r->fetchRow()) {
			// Build array of information we need
			$i = 0;
			foreach ($r as $row) {
				$details[$i]['option'] = $row['optionName'];
				$details[$i]['ipAddress'] = $row['ipAddress'];
				$details[$i]['date'] = $this->formatDate($row['timestamp']);
				$details[$i]['user'] = $row['uName'];
				$current_survey = $row['question'];
				$i++;
			}
		} else { // If there is no user-submitted information pertaining to this survey, just get the name
			$q = 'SELECT question FROM btSurvey WHERE bID = ?';
			$v = array($bID);
			$r = $db->query($q, $v);
			if ($row = $r->fetchRow()) {
				$current_survey = $row['question'];
			}
		}
		// Store local data in larger scope
		$this->set('survey_details', $details);
		$this->set('current_survey', $current_survey);
	}
}