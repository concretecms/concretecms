<?php  $uh = Loader::helper('concrete/urls'); ?>

<ul class="ccm-dialog-tabs" id="ccm-formblock-tabs">
	<li class="<?php echo (intval($miniSurveyInfo['bID'])==0)?'ccm-nav-active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-add">Add</a></li>
	<li class="<?php echo (intval($miniSurveyInfo['bID'])>0)?'ccm-nav-active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-edit">Edit</a></li>
	<li><a href="javascript:void(0)" id="ccm-formblock-tab-preview">Preview</a></li>
	<li><a href="javascript:void(0)" id="ccm-formblock-tab-options">Options</a></li>
</ul>

<input type="hidden" name="miniSurveyServices" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/services.php" />

<div id="ccm-formBlockPane-options" class="ccm-formBlockPane">

	<?php 
	global $c;
	if(strlen($miniSurveyInfo['surveyName'])==0)
		$miniSurveyInfo['surveyName']=$c->getCollectionName();
	?>
	<strong>Form Name: <input id="ccmSurveyName" name="surveyName" style="width: 95%" type="text" class="ccm-input-text" value="<?php echo $miniSurveyInfo['surveyName']?>" /></strong>
	
	<div style="margin-top:16px">
		Notify me by email when people submit this form: 
		<input name="notifyMeOnSubmission" type="checkbox" value="1" <?php echo (intval($miniSurveyInfo['notifyMeOnSubmission'])>=1)?'checked':''?> onchange="miniSurvey.showRecipient(this)" onclick="miniSurvey.showRecipient(this)" />
		<div id="recipientEmailWrap" style=" <?php echo (intval($miniSurveyInfo['notifyMeOnSubmission'])==0)?'display:none':''?>">
			Recipient Email: <input name="recipientEmail" value="<?php echo $miniSurveyInfo['recipientEmail']?>" type="text" size="20" maxlength="128" />
		</div>
	</div> 
</div> 

<input type="hidden" id="qsID" name="qsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />          
<input type="hidden" id="bID" name="bID" type="text" value="<?php echo intval($miniSurveyInfo['bID'])?>" />            
<input type="hidden" id="msqID" name="msqID" type="text" value="<?php echo intval($msqID)?>" />        

<div id="ccm-formBlockPane-add" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])==0)?'display:block':''?> ">
	<div id="newQuestionBox">
	
		<div id="addNewQuestionTitle"><strong>Add a New Question:</strong></div>		
		
		<div id="questionAddedMsg" class="formBlockQuestionMsg">Your question has been added. To view it click the preview tab.</div>
		
		<div class="fieldRow">
			<div class="fieldLabel">Question:</div>
			<div class="fieldValues">
				<input id="question" name="question" type="text" style="width: 265px" class="ccm-input-text" />
			</div>
			<div class="spacer"></div>
		</div>	
		
		<div class="fieldRow">
			<div class="fieldLabel">Answer Type: </div>
			<div class="fieldValues">
				<input name="answerType" type="radio" value="field" /> Text Field &nbsp; <br>
				<input name="answerType" type="radio" value="text" /> Text Area &nbsp; <br>
				<input name="answerType" type="radio" value="radios" /> Radio Buttons &nbsp; <br>
				<input name="answerType" type="radio" value="select" /> Select Box &nbsp; <Br>
				<input name="answerType" type="radio" value="list" /> List Box &nbsp; 				
			</div>
			<div class="spacer"></div>
		</div>
		
		<div class="fieldRow" id="answerOptionsArea">
			<div class="fieldLabel">Answer Options: </div>
			<div class="fieldValues">
				<textarea id="answerOptions" name="answerOptions" cols="50" rows="4" style="width:90%"></textarea><br />
				Put each answer options on a new line			
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
	
<div id="ccm-formBlockPane-edit" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])>0)?'display:block':''?> ">
	
	<div id="questionEditedMsg" class="formBlockQuestionMsg">Your question has been edited.</div>
	
	<div id="editQuestionForm" style="display:none">
		<div id="editQuestionTitle" ><strong>Edit Question:</strong></div>
		
		<div class="fieldRow">
			<div class="fieldLabel">Question:</div>
			<div class="fieldValues">
				<input id="questionEdit" name="question" type="text" style="width: 265px" class="ccm-input-text" />
			</div>
			<div class="spacer"></div>
		</div>	
		
		<div class="fieldRow">
			<div class="fieldLabel">Answer Type: </div>
			<div class="fieldValues">
				<input name="answerTypeEdit" type="radio" value="field" /> Text Field &nbsp; <br>
				<input name="answerTypeEdit" type="radio" value="text" /> Text Area &nbsp; <br>
				<input name="answerTypeEdit" type="radio" value="radios" /> Radio Buttons &nbsp; <br>
				<input name="answerTypeEdit" type="radio" value="select" /> Select Box &nbsp; <Br>
				<input name="answerTypeEdit" type="radio" value="list" /> List Box &nbsp; 				
			</div>
			<div class="spacer"></div>
		</div>
		
		<div class="fieldRow" id="answerOptionsAreaEdit">
			<div class="fieldLabel">Answer Options: </div>
			<div class="fieldValues">
				<textarea id="answerOptionsEdit" name="answerOptionsEdit" cols="50" rows="4" style="width:90%"></textarea><br />
				Put each answer options on a new line			
			</div>
			<div class="spacer"></div>
		</div>
			
		<input id="cancelEditQuestion" name="cancelEdit" type="button" value="Cancel"/>
		<input id="editQuestion" name="edit" type="button" value="Save Changes &raquo;"/>
	</div>

	<div id="miniSurvey">
		<div style="margin-bottom:16px"><strong>Edit:</strong>	</div>
		<div id="miniSurveyWrap"></div>
	</div>
</div>	
	
<div id="ccm-formBlockPane-preview" class="ccm-formBlockPane">
	<div id="miniSurvey">
		<div style="margin-bottom:16px"><strong>Preview:</strong></div>	
		<div id="miniSurveyPreviewWrap"></div>
	</div>
</div>

<script>
//safari was loading the auto.js too late. This ensures it's initialized
function initFormBlockWhenReady(){
	if(miniSurvey && typeof(miniSurvey.init)=='function'){
		miniSurvey.cID=parseInt(<?php echo $c->getCollectionID()?>);
		miniSurvey.arHandle="<?php echo $_REQUEST['arHandle']?>";
		miniSurvey.bID=thisbID;
		miniSurvey.btID=thisbtID;
		miniSurvey.qsID=parseInt(<?php echo $miniSurveyInfo['questionSetId']?>);	
		miniSurvey.init();
		miniSurvey.refreshSurvey();
	}else setTimeout('initFormBlockWhenReady()',100);
}
initFormBlockWhenReady();
</script>