<?
//$miniSurveyInfo['surveyName']= $bs->surveyName;
$miniSurvey=new MiniSurvey($b);
$miniSurveyInfo=$miniSurvey->getMiniSurveyBlockInfo( $b->getBlockID() );

$u=new User();
$ui=UserInfo::getByID($u->uID);
if( strlen(trim($miniSurveyInfo['recipientEmail']))==0 )
	$miniSurveyInfo['recipientEmail']=$ui->uEmail;
?>

<script>
var thisbID=parseInt(<?=$b->getBlockID()?>); 
var thisbtID=parseInt(<?=$b->getBlockTypeID()?>); 
</script>

<? include(DIR_FILES_BLOCK_TYPES_CORE.'/form/styles_include.php'); ?>
  
<? include(DIR_FILES_BLOCK_TYPES_CORE.'/form/form_setup_html.php'); ?>