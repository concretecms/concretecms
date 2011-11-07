<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $uh ConcreteUrlsHelper */ 
$uh = Loader::helper('concrete/urls');
/* @var $fh ConcreteInterfaceFormHelper */
$fh = Loader::helper('concrete/interface/form');
?>
<div class="ccm-ui">
	<ul class="tabs" id="ccm-formblock-tabs">
		<li class="<?php echo (intval($miniSurveyInfo['bID'])==0)?'active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-add"><?php echo t('Add')?></a></li>
		<li class="<?php echo (intval($miniSurveyInfo['bID'])>0)?'active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-edit"><?php echo t('Edit')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-formblock-tab-preview"><?php echo t('Preview')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-formblock-tab-options"><?php echo t('Options')?></a></li>
	</ul>
	<div class="spacer"></div>
	
	<input type="hidden" name="miniSurveyServices" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/services.php" />
	
	<? /* these question ids have been deleted, or edited, and so shouldn't be duplicated for block versioning */ ?>
	<input type="hidden" id="ccm-ignoreQuestionIDs" name="ignoreQuestionIDs" value="" />
	<input type="hidden" id="ccm-pendingDeleteIDs" name="pendingDeleteIDs" value="" />
	
	<div id="ccm-formBlockPane-options" class="ccm-formBlockPane">
		<?php 
		$c = Page::getCurrentPage();
		if(strlen($miniSurveyInfo['surveyName'])==0)
			$miniSurveyInfo['surveyName']=$c->getCollectionName();
		?>
		<fieldset>
			<legend><?=t('Options')?></legend>
			<?=$fh->text(array(
				'name' => 'surveyName',
				'label' => t('Form Name'),
				'value' => $miniSurveyInfo['surveyName']
			))?>
			<?=$fh->textarea(array(
				'name' => 'thankyouMsg',
				'label' => t('Message to display when completed'),
				'value' => $this->controller->thankyouMsg
			))?>
			<?=$fh->text(array(
				'name' => 'recipientEmail',
				'label' => t('Notify me by email when people submit this form'),
				'value' => $miniSurveyInfo['recipientEmail'],
				'help' => t('(Seperate multiple emails with a comma)')
			))?>
			<?=$fh->radios(array(
				'name' => 'displayCaptcha',
				'label' => t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha'),
				'value' => (int) $miniSurveyInfo['displayCaptcha'],
				'options' => array(1 => t('Yes'), 0 => t('No'))
			))?>
			<div class="clearfix">
				<label for="ccm-form-redirect"><?=t('Redirect to another page after form submission?')?></label>
				<div class="input">
					<div id="ccm-form-redirect-page">
						<?php
							$form = Loader::helper('form/page_selector');
							if ($miniSurveyInfo['redirectCID']) {
								print $form->selectPage('redirectCID', $miniSurveyInfo['redirectCID']);
							} else {
								print $form->selectPage('redirectCID');
							}
						?>
					</div>
				</div>
			</div>
		</fieldset>
	</div> 
	
	<input type="hidden" id="qsID" name="qsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />
	<input type="hidden" id="oldQsID" name="oldQsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />
	<input type="hidden" id="bID" name="bID" type="text" value="<?php echo intval($miniSurveyInfo['bID'])?>" />
	<input type="hidden" id="msqID" name="msqID" type="text" value="<?php echo intval($msqID)?>" />
	
	<div id="ccm-formBlockPane-add" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])==0)?'display:block':''?> ">
		
	
		<fieldset id="newQuestionBox">
			<legend id="addNewQuestionTitle"><?php echo t('Add a New Question')?></legend>		
			
			<div id="questionAddedMsg" class="alert-message" style="display:none">
				<?=t('Your question has been added. To view it click the preview tab.')?>
			</div>
			
			<?=$fh->text(array('name' => 'question', 'label' => t('Question')))?>

			<?=$fh->radios(array(
				'name' => 'answerType',
				'label' => t('Answer Type'),
				'options' => array(
					'field' => t('Text Field'),
					'text' => t('Text Area'),
					'radios' => t('Radio Buttons'),
					'select' => t('Select Box'),
					'checkboxlist' => t('Checkbox List'),
					'fileupload' => t('File Upload'),
					'email' => t('Email Address'),
					'telephone' => t('Telephone'),
					'url' => t('Web Address'),
				)
			))?>
			
			<div id="answerOptionsArea">
				<?=$fh->textarea(array(
					'name' => 'answerOptions',
					'label' => t('Answer Options'),
					'help' => t('Put each answer options on a new line')
				))?>
			</div>

			<div id="answerSettings">
				<fieldset>
					<legend><?=t('Settings')?></legend>
					<?=$fh->text(array(
						'name' => 'width',
						'label' => t('Text Area Width'),
						'value' => 50
					))?>
					<?=$fh->text(array(
						'name' => 'height',
						'label' => t('Text Area Height'),
						'value' => 3
					))?>
				</fieldset>
			</div>
			
			<?=$fh->radios(array(
				'name' => 'required',
				'label' => t('Required'),
				'value' => 0,
				'options' => array(
					1 => t('Yes'),
					0 => t('No'),
				)
			))?>
			
			<div>
				<?=$fh->jsbutton('refreshButton', t('Refresh'), '', array('style'=>'display:none'))?>
				<?=$fh->jsbutton('addQuestion', t('Add Question'), 'primary')?>
			</div>
		</fieldset> 
	</div> 
		
	<div id="ccm-formBlockPane-edit" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])>0)?'display:block':''?> ">
		
		<div id="questionEditedMsg" class="alert-message" style="display:none">
			<?php echo t('Your question has been edited.')?>
		</div>
		
		<div id="editQuestionForm" style="display:none">
			<fieldset>
				<legend id="editQuestionTitle"><?=t('Edit Question')?></legend>
				<?=$fh->text(array(
					'id' => 'questionEdit',
					'name' => 'question',
					'label' => t('Question')
				))?>
				
				<?=$fh->radios(array(
					'name' => 'answerTypeEdit',
					'label' => t('Answer Type'),
					'options' => array(
						'field' => t('Text Field'),
						'text' => t('Text Area'),
						'radios' => t('Radio Buttons'),
						'select' => t('Select Box'),
						'checkboxlist' => t('Checkbox List'),
						'fileupload' => t('File Upload'),
						'email' => t('Email Address'),
						'telephone' => t('Telephone'),
						'url' => t('Web Address'),
					)
				))?>
				
				<div id="answerOptionsAreaEdit">
					<?=$fh->textarea(array(
						'id' => 'answerOptionsEdit',
						'name' => 'answerOptions',
						'label' => t('Answer Options'),
						'help' => t('Put each answer options on a new line')
					))?>
				</div>
				
				<div id="answerSettingsEdit">
					<fieldset>
						<legend><?=t('Settings')?></legend>
						<?=$fh->text(array(
							'id' => 'widthEdit',
							'name' => 'width',
							'label' => t('Text Area Width'),
							'value' => 50
						))?>
						<?=$fh->text(array(
							'id' => 'heightEdit',
							'name' => 'height',
							'label' => t('Text Area Height'),
							'value' => 3
						))?>
					</fieldset>
				</div>
				
				<?=$fh->radios(array(
					'id' => 'requiredEdit',
					'name' => 'required',
					'label' => t('Required'),
					'value' => 0,
					'options' => array(
						1 => t('Yes'),
						0 => t('No'),
					)
				))?>
			</fieldset>
			
			<input type="hidden" id="positionEdit" name="position" type="text" value="1000" />
			
			<?=$fh->jsbutton('cancelEditQuestion', t('Cancel'))?>
			<?=$fh->jsbutton('editQuestion', t('Save Changes'), 'primary')?>
		</div>
	
		<div id="miniSurvey">
			<div style="margin-bottom:16px"><strong><?php echo t('Edit')?>:</strong>	</div>
			<div id="miniSurveyWrap"></div>
		</div>
	</div>	
		
	<div id="ccm-formBlockPane-preview" class="ccm-formBlockPane">
		<div id="miniSurvey">
			<div style="margin-bottom:16px"><strong><?php echo t('Preview')?>:</strong></div>	
			<div id="miniSurveyPreviewWrap"></div>
		</div>
	</div>
</div>
<script>
//safari was loading the auto.js too late. This ensures it's initialized
function initFormBlockWhenReady(){
	if(miniSurvey && typeof(miniSurvey.init)=='function'){
		miniSurvey.cID=parseInt(<?php echo $c->getCollectionID()?>);
		miniSurvey.arHandle="<?php echo $a->getAreaHandle()?>";
		miniSurvey.bID=thisbID;
		miniSurvey.btID=thisbtID;
		miniSurvey.qsID=parseInt(<?php echo $miniSurveyInfo['questionSetId']?>);	
		miniSurvey.init();
		miniSurvey.refreshSurvey();
	}else setTimeout('initFormBlockWhenReady()',100);
}
initFormBlockWhenReady();
</script>
