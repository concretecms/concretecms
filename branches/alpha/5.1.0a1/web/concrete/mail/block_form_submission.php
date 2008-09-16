
There has been a submission of the form "<?=$formName?>" on through your Concrete5 website.

<? 
foreach($questionAnswerPairs as $questionAnswerPair){
	echo $questionAnswerPair['question']."\r\n".$questionAnswerPair['answer']."\r\n"."\r\n";
} 
?>

To view all of this form's submissions, visit <?=BASE_URL.DIR_REL?>/index.php/dashboard/form_results/?qsid=<?=$questionSetId?>
