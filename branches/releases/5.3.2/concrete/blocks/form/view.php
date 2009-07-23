<?php    
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
.miniSurveyView #msg .error{padding-left:16px; color:#cc0000}
.miniSurveyView table.formBlockSurveyTable td img.ccm-captcha-image{float:none}
.miniSurveyView .required{ color:#cc0000 }
</style>
<?php  if ($invalidIP) { ?>
<div class="ccm-error"><p><?php echo $invalidIP?></p></div>
<?php  } ?>
<form enctype="multipart/form-data" id="miniSurveyView<?php echo intval($bID)?>" class="miniSurveyView" method="post" action="<?php  echo $this->action('submit_form')?>">
	<?php   if( $_GET['surveySuccess'] && $_GET['qsid']==intval($survey->questionSetId) ){ ?>
		<div id="msg"><?php  echo $survey->thankyouMsg ?></div> 
	<?php   }elseif(strlen($formResponse)){ ?>
		<div id="msg">
			<?php  echo $formResponse ?>
			<?php  
			if(is_array($errors) && count($errors)) foreach($errors as $error){
				echo '<div class="error">'.$error.'</div>';
			} ?>
		</div>
	<?php  } ?>
	<input name="qsID" type="hidden" value="<?php echo  intval($survey->questionSetId)?>" />
	<input name="pURI" type="hidden" value="<?php echo  $pURI ?>" />
	<?php   $miniSurvey->loadSurvey( $survey->questionSetId, 0, intval($bID) );  ?> 
</form>