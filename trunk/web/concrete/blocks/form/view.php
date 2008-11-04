<?  
defined('C5_EXECUTE') or die(_("Access Denied."));
$survey=$controller;  
//echo $survey->surveyName.'<br>';
$miniSurvey=new MiniSurvey($b);
$miniSurvey->frontEndMode=true;
?>
<style>
.miniSurveyView{ margin-bottom:16px}
.miniSurveyView #msg{ background:#FFFF99; color: #000; padding:2px; border:1px solid #999; margin:8px 0px 8px 0px}
.miniSurveyView table.formBlockSurveyTable td{ padding-bottom:4px }
.miniSurveyView td.question {padding-right: 12px}
</style>

<form id="miniSurveyView<?=intval($survey->questionSetId)?>" class="miniSurveyView" method="post" action="<?=$this->action('submit_form')?>">
	<div style="margin-bottom:8px"><strong><?=$survey->surveyName?></strong></div>
	<? if( $_GET['surveySuccess'] && $_GET['qsid']==intval($survey->questionSetId) ){ ?>
		<div id="msg"><?=t("Thanks for taking the time to report a problem or ask a question. We're on it and you'll receive a response soon!")?></div> 
	<? } ?>
	<input name="qsID" type="hidden" value="<?=intval($survey->questionSetId)?>" />
	<input name="pURI" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>" />
	<? $miniSurvey->loadSurvey( $survey->questionSetId ); ?>
</form>