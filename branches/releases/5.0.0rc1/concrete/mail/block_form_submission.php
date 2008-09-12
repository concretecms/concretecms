
There has been a submission of the form "<?php echo $formName?>" on through your Concrete5 website.

<?php  
foreach($questionAnswerPairs as $questionAnswerPair){
	echo $questionAnswerPair['question']."\r\n".$questionAnswerPair['answer']."\r\n"."\r\n";
} 
?>

To view all of this form's submissions, visit <?php echo BASE_URL.DIR_REL?>/index.php/dashboard/form_results/?qsid=<?php echo $questionSetId?>
