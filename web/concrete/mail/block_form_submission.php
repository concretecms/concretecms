<? 
defined('C5_EXECUTE') or die("Access Denied.");

$submittedData='';
foreach($questionAnswerPairs as $questionAnswerPair){
	$submittedData .= $questionAnswerPair['question']."\r\n".$questionAnswerPair['answer']."\r\n"."\r\n";
} 
$formDisplayUrl=BASE_URL.DIR_REL.'/' . DISPATCHER_FILENAME . '/dashboard/reports/forms/?qsid='.$questionSetId;

$body = t("
There has been a submission of the form %s through your concrete5 website.

%s

To view all of this form's submissions, visit %s 

", $formName, $submittedData, $formDisplayUrl);