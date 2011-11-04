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
			<?=$fh->text('surveyName', t('Form Name'), $miniSurveyInfo['surveyName'])?>
			<?=$fh->textarea('thankyouMsg', t('Message to display when completed'), $this->controller->thankyouMsg)?>
			<?=$fh->text('recipientEmail', t('Notify me by email when people submit this form'), $miniSurveyInfo['recipientEmail'], t('(Seperate multiple emails with a comma)'))?>
			<?=$fh->radios('displayCaptcha', t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha'), array(
				1 => t('Yes'),
				0 => t('No'),
			), (int) $miniSurveyInfo['displayCaptcha'])?>
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
			
			<?=$fh->text('question', t('Question'))?>
			
			<?=$fh->radios('answerType', t('Answer Type'), array(
				'field' => t('Text Field'),
				'text' => t('Text Area'),
				'radios' => t('Radio Buttons'),
				'select' => t('Select Box'),
				'checkboxlist' => t('Checkbox List'),
				'fileupload' => t('File Upload'),
				'email' => t('Email Address'),
				'telephone' => t('Telephone'),
				'url' => t('Web Address'),
			))?>
			
			<div id="answerOptionsArea">
				<?=$fh->textarea('answerOptions', t('Answer Options'), null, t('Put each answer options on a new line'))?>
			</div>

			<div id="answerSettings">
				<fieldset>
					<legend><?=t('Settings')?></legend>
					<?=$fh->text('width', 'Text Area Width', 50)?>
					<?=$fh->text('height', 'Text Area Height', 3)?>
				</fieldset>
			</div>
			
			<div id="questionRequired">
				<?=$fh->radios('required', t('Required'), array(
					1 => t('Yes'),
					0 => t('No'),
				), 0)?>
			</div>
			
			<div>
				<?=$fh->jsbutton('refreshButton', t('Refresh'), '', array('style'=>'display:none'))?>
				<?=$fh->jsbutton('addQuestion', t('Add Question'), 'primary')?>
			</div>
		</fieldset> 
	</div> 
		
	<div id="ccm-formBlockPane-edit" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])>0)?'display:block':''?> ">
		
		<div id="questionEditedMsg" class="formBlockQuestionMsg"><?php echo t('Your question has been edited.')?></div>
		
		<div id="editQuestionForm" style="display:none">
			<div id="editQuestionTitle" ><strong><?php echo t('Edit Question')?>:</strong></div>
			
			<div class="fieldRow">
				<div class="fieldLabel"><?php echo t('Question')?>:</div>
				<div class="fieldValues">
					<input id="questionEdit" name="question" type="text" style="width: 265px" class="ccm-input-text" />
				</div>
				<div class="ccm-spacer"></div>
			</div>	
			
			<div class="fieldRow">
				<div class="fieldLabel"><?php echo t('Answer Type')?>: </div>
				<div class="fieldValues">
					<input name="answerTypeEdit" type="radio" value="field" /> <?php echo t('Text Field')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="text" /> <?php echo t('Text Area')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="radios" /> <?php echo t('Radio Buttons')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="select" /> <?php echo t('Select Box')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="checkboxlist" /> <?php echo t('Checkbox List')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="fileupload" /> <?php echo t('File Upload')?>
					<input name="answerTypeEdit" type="radio" value="email" /> <?php echo t('Email Address')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="telephone" /> <?php echo t('Telephone')?> &nbsp; <br>
					<input name="answerTypeEdit" type="radio" value="url" /> <?php echo t('Web Address')?> &nbsp; <br>
				</div>
				<div class="ccm-spacer"></div>
			</div>
			
			<div class="fieldRow" id="answerOptionsAreaEdit">
				<div class="fieldLabel"><?php echo t('Answer Options')?>: </div>
				<div class="fieldValues">
					<textarea id="answerOptionsEdit" name="answerOptionsEdit" cols="50" rows="4" style="width:90%"></textarea><br />
					<?php echo t('Put each answer options on a new line')?>			
				</div>
				<div class="ccm-spacer"></div>
			</div>
				
			<div class="fieldRow" id="answerSettingsEdit">
				<div class="fieldLabel"><?php echo t('Settings')?>: </div>
				<div class="fieldValues">
					<?php echo t('Text Area Width')?>: <input id="widthEdit" name="width" type="text" value="50" size="3"/> <br />
					<?php echo t('Text Area Height')?>: <input id="heightEdit" name="height" type="text" value="3" size="2"/>
				</div>
				<div class="ccm-spacer"></div>
			</div>
			
			<div class="fieldRow" id="questionRequired">
				<div class="fieldLabel">&nbsp;</div>
				<div class="fieldValues"> 
					<input id="requiredEdit" name="required" type="checkbox" value="1" />
					<?php echo t('This question is required.')?> 
				</div>
				<div class="ccm-spacer"></div>
			</div>		
			
			<input type="hidden" id="positionEdit" name="position" type="text" value="1000" />
			<input id="cancelEditQuestion" name="cancelEdit" type="button" value="Cancel"/>
			<input id="editQuestion" name="edit" type="button" value="Save Changes &raquo;"/>
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
