<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$uh = Loader::helper('concrete/urls'); ?>

<ul class="ccm-dialog-tabs" id="ccm-formblock-tabs">
	<li class="<?=(intval($miniSurveyInfo['bID'])==0)?'ccm-nav-active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-add"><?=t('Add')?></a></li>
	<li class="<?=(intval($miniSurveyInfo['bID'])>0)?'ccm-nav-active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-edit"><?=t('Edit')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-formblock-tab-preview"><?=t('Preview')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-formblock-tab-options"><?=t('Options')?></a></li>
</ul>

<input type="hidden" name="miniSurveyServices" value="<?=$uh->getBlockTypeToolsURL($bt)?>/services.php" />

<div id="ccm-formBlockPane-options" class="ccm-formBlockPane">

	<?
	global $c;
	if(strlen($miniSurveyInfo['surveyName'])==0)
		$miniSurveyInfo['surveyName']=$c->getCollectionName();
	?>
	<strong><?=t('Form Name')?>: <input id="ccmSurveyName" name="surveyName" style="width: 95%" type="text" class="ccm-input-text" value="<?=$miniSurveyInfo['surveyName']?>" /></strong>
	
	<div style="margin-top:16px">
		<?=t('Notify me by email when people submit this form')?>: 
		<input name="notifyMeOnSubmission" type="checkbox" value="1" <?=(intval($miniSurveyInfo['notifyMeOnSubmission'])>=1)?'checked':''?> onchange="miniSurvey.showRecipient(this)" onclick="miniSurvey.showRecipient(this)" />
		<div id="recipientEmailWrap" style=" <?=(intval($miniSurveyInfo['notifyMeOnSubmission'])==0)?'display:none':''?>">
			<?=t('Recipient Email')?>: <input name="recipientEmail" value="<?=$miniSurveyInfo['recipientEmail']?>" type="text" size="20" maxlength="128" />
		</div>
	</div> 
</div> 

<input type="hidden" id="qsID" name="qsID" type="text" value="<?=intval($miniSurveyInfo['questionSetId'])?>" />          
<input type="hidden" id="bID" name="bID" type="text" value="<?=intval($miniSurveyInfo['bID'])?>" />            
<input type="hidden" id="msqID" name="msqID" type="text" value="<?=intval($msqID)?>" />        

<div id="ccm-formBlockPane-add" class="ccm-formBlockPane" style=" <?=(intval($miniSurveyInfo['bID'])==0)?'display:block':''?> ">
	<div id="newQuestionBox">
	
		<div id="addNewQuestionTitle"><strong><?=t('Add a New Question')?>:</strong></div>		
		
		<div id="questionAddedMsg" class="formBlockQuestionMsg"><?=t('Your question has been added. To view it click the preview tab.')?></div>
		
		<div class="fieldRow">
			<div class="fieldLabel"><?=t('Question')?>:</div>
			<div class="fieldValues">
				<input id="question" name="question" type="text" style="width: 265px" class="ccm-input-text" />
			</div>
			<div class="spacer"></div>
		</div>	
		
		<div class="fieldRow">
			<div class="fieldLabel"><?=t('Answer Type')?>: </div>
			<div class="fieldValues">
				<input name="answerType" type="radio" value="field" /> <?=t('Text Field')?> &nbsp; <br>
				<input name="answerType" type="radio" value="text" /> <?=t('Text Area')?> &nbsp; <br>
				<input name="answerType" type="radio" value="radios" /> <?=t('Radio Buttons ')?> &nbsp; <br>
				<input name="answerType" type="radio" value="select" /> <?=t('Select Box')?> &nbsp; <Br>
				<input name="answerType" type="radio" value="list" /> <?=t('List Box')?> &nbsp; 				
			</div>
			<div class="spacer"></div>
		</div>
		
		<div class="fieldRow" id="answerOptionsArea">
			<div class="fieldLabel"><?=t('Answer Options')?>: </div>
			<div class="fieldValues">
				<textarea id="answerOptions" name="answerOptions" cols="50" rows="4" style="width:90%"></textarea><br />
				<?=t('Put each answer options on a new line')?>
			</div>
			<div class="spacer"></div>
		</div>	
		
		<div class="fieldRow" >
			<div class="fieldLabel">&nbsp; </div>
			<div class="fieldValues">
				<input id="refreshButton" name="refresh" type="button" value="Refresh" style="display:none" /> 
				<input id="addQuestion" name="add" type="button" value="Add Question &raquo;" />
			</div>
		</div>
		
		<div class="spacer"></div>
		
	</div> 
</div> 
	
<div id="ccm-formBlockPane-edit" class="ccm-formBlockPane" style=" <?=(intval($miniSurveyInfo['bID'])>0)?'display:block':''?> ">
	
	<div id="questionEditedMsg" class="formBlockQuestionMsg"><?=t('Your question has been edited.')?></div>
	
	<div id="editQuestionForm" style="display:none">
		<div id="editQuestionTitle" ><strong><?=t('Edit Question')?>:</strong></div>
		
		<div class="fieldRow">
			<div class="fieldLabel"><?=t('Question')?>:</div>
			<div class="fieldValues">
				<input id="questionEdit" name="question" type="text" style="width: 265px" class="ccm-input-text" />
			</div>
			<div class="spacer"></div>
		</div>	
		
		<div class="fieldRow">
			<div class="fieldLabel"><?=t('Answer Type')?>: </div>
			<div class="fieldValues">
				<input name="answerTypeEdit" type="radio" value="field" /> <?=t('Text Field')?> &nbsp; <br>
				<input name="answerTypeEdit" type="radio" value="text" /> <?=t('Text Area')?> &nbsp; <br>
				<input name="answerTypeEdit" type="radio" value="radios" /> <?=t('Radio Buttons')?> &nbsp; <br>
				<input name="answerTypeEdit" type="radio" value="select" /> <?=t('Select Box')?> &nbsp; <Br>
				<input name="answerTypeEdit" type="radio" value="list" /> <?=t('List Box')?> &nbsp; 				
			</div>
			<div class="spacer"></div>
		</div>
		
		<div class="fieldRow" id="answerOptionsAreaEdit">
			<div class="fieldLabel"><?=t('Answer Options')?>: </div>
			<div class="fieldValues">
				<textarea id="answerOptionsEdit" name="answerOptionsEdit" cols="50" rows="4" style="width:90%"></textarea><br />
				<?=t('Put each answer options on a new line')?>			
			</div>
			<div class="spacer"></div>
		</div>
			
		<input id="cancelEditQuestion" name="cancelEdit" type="button" value="Cancel"/>
		<input id="editQuestion" name="edit" type="button" value="Save Changes &raquo;"/>
	</div>

	<div id="miniSurvey">
		<div style="margin-bottom:16px"><strong><?=t('Edit')?>:</strong>	</div>
		<div id="miniSurveyWrap"></div>
	</div>
</div>	
	
<div id="ccm-formBlockPane-preview" class="ccm-formBlockPane">
	<div id="miniSurvey">
		<div style="margin-bottom:16px"><strong><?=t('Preview')?>:</strong></div>	
		<div id="miniSurveyPreviewWrap"></div>
	</div>
</div>

<script>
//safari was loading the auto.js too late. This ensures it's initialized
function initFormBlockWhenReady(){
	if(miniSurvey && typeof(miniSurvey.init)=='function'){
		miniSurvey.cID=parseInt(<?=$c->getCollectionID()?>);
		miniSurvey.arHandle="<?=$_REQUEST['arHandle']?>";
		miniSurvey.bID=thisbID;
		miniSurvey.btID=thisbtID;
		miniSurvey.qsID=parseInt(<?=$miniSurveyInfo['questionSetId']?>);	
		miniSurvey.init();
		miniSurvey.refreshSurvey();
	}else setTimeout('initFormBlockWhenReady()',100);
}
initFormBlockWhenReady();
</script>