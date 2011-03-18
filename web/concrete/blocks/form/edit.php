<?php 
defined('C5_EXECUTE') or die("Access Denied.");
//$miniSurveyInfo['surveyName']= $bs->surveyName;
$miniSurvey=new MiniSurvey($b);
$miniSurveyInfo=$miniSurvey->getMiniSurveyBlockInfo( $b->getBlockID() );
MiniSurvey::questionCleanup( intval($miniSurveyInfo['questionSetId']), $b->getBlockID() );

$u=new User();
$ui=UserInfo::getByID($u->uID);
if( strlen(trim($miniSurveyInfo['recipientEmail']))==0 )
	$miniSurveyInfo['recipientEmail']=$ui->uEmail;
?>

<script>
var thisbID=parseInt(<?php echo $b->getBlockID()?>); 
var thisbtID=parseInt(<?php echo $b->getBlockTypeID()?>); 
</script>

<?php  $this->inc('styles_include.php', array('c' => $c, 'b' => $b, 'miniSurveyInfo' => $miniSurveyInfo, 'miniSurvey' => $miniSurvey,  'a' => $a, 'bt' => $bt)); ?>
<?php  $this->inc('form_setup_html.php', array('c' => $c, 'b' => $b, 'miniSurveyInfo' => $miniSurveyInfo, 'miniSurvey' => $miniSurvey, 'a' => $a, 'bt' => $bt)); ?>
