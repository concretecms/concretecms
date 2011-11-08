<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $uh ConcreteUrlsHelper */ 
$uh = Loader::helper('concrete/urls');
/* @var $form FormHelper */
$form = Loader::helper('form');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
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
			<div class="clearfix">
				<?=$form->label('surveyName', t('Form Name'))?>
				<div class="input">
					<?=$form->text('surveyName', $miniSurveyInfo['surveyName'])?>
				</div>
			</div>
			<div class="clearfix">
				<?=$form->label('thankyouMsg', t('Message to display when completed'))?>
				<div class="input">
					<?=$form->textarea('thankyouMsg', $this->controller->thankyouMsg, array('rows' => 3))?>
				</div>
			</div>
			<div class="clearfix">
				<?=$form->label('recipientEmail', t('Notify me by email when people submit this form'))?>
				<div class="input">
					<div class="input-prepend">
						<label class="add-on">
							<?=$form->checkbox('notifyMeOnSubmission', 1, $miniSurveyInfo['notifyMeOnSubmission'] == 1)?>
						</label>
						<?=$form->text('recipientEmail', $miniSurveyInfo['recipientEmail'])?>
					</div>

					<span class="help-block"><?=t('(Seperate multiple emails with a comma)')?></span>
				</div>
			</div>
			<div class="clearfix">
				<label><?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha')?></label>
				<div class="input">
					<ul class="inputs-list" id="displayCaptcha">
						<li>
							<label>
								<?=$form->radio('displayCaptcha', 1, (int) $miniSurveyInfo['displayCaptcha'])?>
								<span><?=t('Yes')?></span>
							</label>
						</li>
						<li>
							<label>
								<?=$form->radio('displayCaptcha', 0, (int) $miniSurveyInfo['displayCaptcha'])?>
								<span><?=t('No')?></span>
							</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix">
				<label for="ccm-form-redirect"><?=t('Redirect to another page after form submission?')?></label>
				<div class="input">
					<div id="ccm-form-redirect-page">
						<?php
							$page_selector = Loader::helper('form/page_selector');
							if ($miniSurveyInfo['redirectCID']) {
								print $page_selector->selectPage('redirectCID', $miniSurveyInfo['redirectCID']);
							} else {
								print $page_selector->selectPage('redirectCID');
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

			<div class="clearfix">
				<?=$form->label('question', t('Question'))?>
				<div class="input">
					<?=$form->text('question')?>
				</div>
			</div>
			<div class="clearfix">
				<label><?=t('Answer Type')?></label>
				<div class="input">
					<ul class="inputs-list" id="answerType">
						<li><label>
							<?=$form->radio('answerType', 'field')?>
							<span><?=t('Text Field')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'text')?>
							<span><?=t('Text Area')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'radios')?>
							<span><?=t('Radio Buttons')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'select')?>
							<span><?=t('Select Box')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'checkboxlist')?>
							<span><?=t('Checkbox List')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'fileupload')?>
							<span><?=t('File Upload')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'email')?>
							<span><?=t('Email Address')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'telephone')?>
							<span><?=t('Telephone')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('answerType', 'url')?>
							<span><?=t('Web Address')?></span>
						</label></li>
					</ul>
				</div>
			</div>
			
			<div id="answerOptionsArea">
				<div class="clearfix">
					<?=$form->label('answerOptions', t('Answer Options'))?>
					<div class="input">
						<?=$form->textarea('answerOptions', array('rows' => 3))?>
						<span class="help-block"><?=t('Put each answer options on a new line')?></span>
					</div>
				</div>
			</div>

			<div id="answerSettings">
				<div class="clearfix">
					<?=$form->label('width', t('Text Area Width'))?>
					<div class="input">
						<?=$form->text('width', 50)?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('height', t('Text Area Height'))?>
					<div class="input">
						<?=$form->text('height', 3)?>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<label><?=t('Required')?></label>
				<div class="input">
					<ul class="inputs-list" id="required">
						<li><label>
							<?=$form->radio('required', 1)?>
							<span><?=t('Yes')?></span>
						</label></li>
						<li><label>
							<?=$form->radio('required', 0)?>
							<span><?=t('No')?></span>
						</label></li>
					</ul>
				</div>
			</div>
			
			<div>
				<?=$ih->button(t('Refresh'), '#', 'left', '', array('style' => 'display:none', 'id' => 'refreshButton'))?>
				<?=$ih->button(t('Add Question'), '#', 'right', 'primary', array('id' => 'addQuestion'))?>
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
				<div class="clearfix">
					<?=$form->label('question', t('Question'))?>
					<div class="input">
						<?=$form->text('questionEdit')?>
					</div>
				</div>

				<div class="clearfix">
					<label><?=t('Answer Type')?></label>
					<div class="input">
						<ul class="inputs-list" id="answerTypeEdit">
							<li><label>
								<?=$form->radio('answerTypeEdit', 'field')?>
								<span><?=t('Text Field')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'text')?>
								<span><?=t('Text Area')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'radios')?>
								<span><?=t('Radio Buttons')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'select')?>
								<span><?=t('Select Box')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'checkboxlist')?>
								<span><?=t('Checkbox List')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'fileupload')?>
								<span><?=t('File Upload')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'email')?>
								<span><?=t('Email Address')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'telephone')?>
								<span><?=t('Telephone')?></span>
							</label></li>
							<li><label>
								<?=$form->radio('answerTypeEdit', 'url')?>
								<span><?=t('Web Address')?></span>
							</label></li>
						</ul>
					</div>
				</div>
				
				<div id="answerOptionsAreaEdit">
					<div class="clearfix">
						<?=$form->label('answerOptionsEdit', t('Answer Options'))?>
						<div class="input">
							<?=$form->textarea('answerOptionsEdit', array('rows' => 3))?>
							<span class="help-block"><?=t('Put each answer options on a new line')?></span>
						</div>
					</div>
				</div>
				
				<div id="answerSettingsEdit">
					<div class="clearfix">
						<?=$form->label('widthEdit', t('Text Area Width'))?>
						<div class="input">
							<?=$form->text('widthEdit', 50)?>
						</div>
					</div>
					<div class="clearfix">
						<?=$form->label('heightEdit', t('Text Area Height'))?>
						<div class="input">
							<?=$form->text('heightEdit', 3)?>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<label><?=t('Required')?> </label>
					<div class="input">
						<ul class="inputs-list" id="requiredEdit">
							<li><label> <?=$form->radio('requiredEdit', 1)?> <span><?=t('Yes')?>
								</span>
							</label></li>
							<li><label> <?=$form->radio('requiredEdit', 0)?> <span><?=t('No')?> </span>
							</label></li>
						</ul>
					</div>
				</div>
			</fieldset>
			
			<input type="hidden" id="positionEdit" name="position" type="text" value="1000" />
			
			<div>
				<?=$ih->button(t('Cancel'), '#', 'left', '', array('id' => 'cancelEditQuestion'))?>
				<?=$ih->button(t('Save Changes'), '#', 'right', 'primary', array('id' => 'editQuestion'))?>
			</div>
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
