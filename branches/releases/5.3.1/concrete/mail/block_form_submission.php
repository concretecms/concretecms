<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

$submittedData='';
foreach($questionAnswerPairs as $questionAnswerPair){
	$submittedData .= $questionAnswerPair['question']."\r\n".$questionAnswerPair['answer']."\r\n"."\r\n";
} 
$formDisplayUrl=BASE_URL.DIR_REL.'/index.php/dashboard/reports/forms/?qsid='.$questionSetId;

$body = t("
There has been a submission of the form %s on through your Concrete5 website.

%s

To view all of this form's submissions, visit %s 

", $formName, $submittedData, $formDisplayUrl);
?>
