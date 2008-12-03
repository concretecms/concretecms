<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
//$miniSurveyInfo['surveyName']= $bs->surveyName;
$miniSurvey=new MiniSurvey($b);
$miniSurveyInfo=$miniSurvey->getMiniSurveyBlockInfo( $b->getBlockID() );

$u=new User();
$ui=UserInfo::getByID($u->uID);
if( strlen(trim($miniSurveyInfo['recipientEmail']))==0 )
	$miniSurveyInfo['recipientEmail']=$ui->uEmail;
?>

<script>
var thisbID=parseInt(<?php echo $b->getBlockID()?>); 
var thisbtID=parseInt(<?php echo $b->getBlockTypeID()?>); 
</script>

<?php  include(DIR_FILES_BLOCK_TYPES_CORE.'/form/styles_include.php'); ?>
  
<?php  include(DIR_FILES_BLOCK_TYPES_CORE.'/form/form_setup_html.php'); ?>