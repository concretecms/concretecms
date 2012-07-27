<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$survey=$controller;  
//echo $survey->surveyName.'<br>';
$miniSurvey=new MiniSurvey($b);
$miniSurvey->frontEndMode=true;
?>
<a name="<?php echo $survey->questionSetId ?>"></a><br/>
<?php if ($invalidIP) { ?>
<div class="ccm-error"><p><?=$invalidIP?></p></div>
<? } ?>

<form enctype="multipart/form-data" id="miniSurveyView<?=intval($bID)?>" class="miniSurveyView" method="post" action="<?php echo $this->action('submit_form').'#'.$survey->questionSetId?>">
	<?php  if( $_GET['surveySuccess'] && $_GET['qsid']==intval($survey->questionSetId) ){ ?>
		<div id="msg"><?php echo $survey->thankyouMsg ?></div> 
	<?php  }elseif(strlen($formResponse)){ ?>
		<div id="msg">
			<?php echo $formResponse ?>
			<?php 
			if(is_array($errors) && count($errors)) foreach($errors as $error){
				echo '<div class="error">'.$error.'</div>';
			} ?>
		</div>
	<? } ?>
	<input name="qsID" type="hidden" value="<?= intval($survey->questionSetId)?>" />
	<?php  $miniSurvey->loadSurvey( $survey->questionSetId, 0, intval($bID) );  ?> 
</form>