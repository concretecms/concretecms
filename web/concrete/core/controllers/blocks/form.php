<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Blocks
 * @subpackage Form
 * @author Tony Trupp <tony@concrete5.org>
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Controller_Block_Form extends BlockController {

	public $btTable = 'btForm';
	public $btQuestionsTablename = 'btFormQuestions';
	public $btAnswerSetTablename = 'btFormAnswerSet';
	public $btAnswersTablename = 'btFormAnswers'; 	
	public $btInterfaceWidth = '420';
	public $btInterfaceHeight = '430';
	public $thankyouMsg='';
	public $noSubmitFormRedirect=0;
	
	protected $btExportTables = array('btForm', 'btFormQuestions');
	protected $btExportPageColumns = array('redirectCID');
	protected $lastAnswerSetId=0;
		
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Build simple forms and surveys.");
	}
	
	public function getBlockTypeName() {
		return t("Form");
	}
	
	public function getJavaScriptStrings() {
		return array(
			'delete-question' => t('Are you sure you want to delete this question?'),
			'form-name' => t('Your form must have a name.'),
			'complete-required' => t('Please complete all required fields.'),
			'ajax-error' => t('AJAX Error.'),
			'form-min-1' => t('Please add at least one question to your form.')			
		);
	}

	protected function importAdditionalData($b, $blockNode) {
		if (isset($blockNode->data)) {
			foreach($blockNode->data as $data) {
				if ($data['table'] != $this->getBlockTypeDatabaseTable()) {
					$table = (string) $data['table'];
					if (isset($data->record)) {
						foreach($data->record as $record) {
							$aar = new ADODB_Active_Record($table);
							$aar->bID = $b->getBlockID();
							foreach($record->children() as $node) {
								$nodeName = $node->getName();
								$aar->{$nodeName} = (string) $node;
							}
							if ($table == 'btFormQuestions') {
								$db = Loader::db();
								$aar->questionSetId = $db->GetOne('select questionSetId from btForm where bID = ?', array($b->getBlockID()));
							}
							$aar->Save();
						}
					}								
				}
			}
		}
	}
	
	public function __construct($b = null){ 
		parent::__construct($b);
		//$this->bID = intval($this->_bID);
		if(is_string($this->thankyouMsg) && !strlen($this->thankyouMsg)){ 
			$this->thankyouMsg = $this->getDefaultThankYouMsg();
		}
	}
	
	public function on_page_view() {
		if ($this->viewRequiresJqueryUI()) {
			$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
			$this->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));
		}
	}
	
	//Internal helper function
	private function viewRequiresJqueryUI() {
		$whereInputTypes = "inputType = 'date' OR inputType = 'datetime'";
		$sql = "SELECT COUNT(*) FROM {$this->btQuestionsTablename} WHERE questionSetID = ? AND bID = ? AND ({$whereInputTypes})";
		$vals = array(intval($this->questionSetId), intval($this->bID));
		$JQUIFieldCount = Loader::db()->GetOne($sql, $vals);
		return (bool)$JQUIFieldCount;
	}
	
	public function getDefaultThankYouMsg() {
		return t("Thanks!");
	}
	
	//form add or edit submit 
	//(run after the duplicate method on first block edit of new page version)
	function save( $data=array() ) { 
		if( !$data || count($data)==0 ) $data=$_POST;  
		
		$b=$this->getBlockObject(); 
		$c=$b->getBlockCollectionObject();
		
		$db = Loader::db();
		if(intval($this->bID)>0){	 
			$q = "select count(*) as total from {$this->btTable} where bID = ".intval($this->bID);
			$total = $db->getOne($q);
		}else $total = 0; 
			
		if($_POST['qsID']) $data['qsID']=$_POST['qsID'];
		if( !$data['qsID'] ) $data['qsID']=time(); 	
		if(!$data['oldQsID']) $data['oldQsID']=$data['qsID']; 
		$data['bID']=intval($this->bID); 
		
		if(!empty($data['redirectCID'])) {
			$data['redirect'] = 1;
		} else {
			$data['redirect'] = 0;
			$data['redirectCID'] = 0;
		}

		if(empty($data['addFilesToSet'])) {
			$data['addFilesToSet'] = 0;
		}
		
		$v = array( $data['qsID'], $data['surveyName'], intval($data['notifyMeOnSubmission']), $data['recipientEmail'], $data['thankyouMsg'], intval($data['displayCaptcha']), intval($data['redirectCID']), intval($data['addFilesToSet']), intval($this->bID) );
 		
		//is it new? 
		if( intval($total)==0 ){
			$q = "insert into {$this->btTable} (questionSetId, surveyName, notifyMeOnSubmission, recipientEmail, thankyouMsg, displayCaptcha, redirectCID, addFilesToSet, bID) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		}else{
			$q = "update {$this->btTable} set questionSetId = ?, surveyName=?, notifyMeOnSubmission=?, recipientEmail=?, thankyouMsg=?, displayCaptcha=?, redirectCID=?, addFilesToSet=? where bID = ? AND questionSetId=".$data['qsID'];
		}
		
		$rs = $db->query($q,$v);  
		
		//Add Questions (for programmatically creating forms, such as during the site install)
		if( count($data['questions'])>0 ){
			$miniSurvey = new MiniSurvey();
			foreach( $data['questions'] as $questionData )
				$miniSurvey->addEditQuestion($questionData,0);
		}
 
 		$this->questionVersioning($data);
		
		return true;
	}
	
	//Ties the new or edited questions to the new block number
	//New and edited questions are temporarily given bID=0, until the block is saved... painfully complicated
	protected function questionVersioning( $data=array() ){
		$db = Loader::db();
		$oldBID = intval($data['bID']);
		
		//if this block is being edited a second time, remove edited questions with the current bID that are pending replacement
		//if( intval($oldBID) == intval($this->bID) ){  
			$vals=array( intval($data['oldQsID']) );  
			$pendingQuestions=$db->getAll('SELECT msqID FROM btFormQuestions WHERE bID=0 && questionSetId=?',$vals); 
			foreach($pendingQuestions as $pendingQuestion){  
				$vals=array( intval($this->bID), intval($pendingQuestion['msqID']) );  
				$db->query('DELETE FROM btFormQuestions WHERE bID=? AND msqID=?',$vals);
			}
		//} 
	
		//assign any new questions the new block id 
		$vals=array( intval($data['bID']), intval($data['qsID']), intval($data['oldQsID']) );  
		$rs=$db->query('UPDATE btFormQuestions SET bID=?, questionSetId=? WHERE bID=0 && questionSetId=?',$vals);
 
 		//These are deleted or edited questions.  (edited questions have already been created with the new bID).
 		$ignoreQuestionIDsDirty=explode( ',', $data['ignoreQuestionIDs'] );
		$ignoreQuestionIDs=array(0);
		foreach($ignoreQuestionIDsDirty as $msqID)
			$ignoreQuestionIDs[]=intval($msqID);	
		$ignoreQuestionIDstr=join(',',$ignoreQuestionIDs); 
		
		//remove any questions that are pending deletion, that already have this current bID 
 		$pendingDeleteQIDsDirty=explode( ',', $data['pendingDeleteIDs'] );
		$pendingDeleteQIDs=array();
		foreach($pendingDeleteQIDsDirty as $msqID)
			$pendingDeleteQIDs[]=intval($msqID);		
		$vals=array( $this->bID, intval($data['qsID']), join(',',$pendingDeleteQIDs) );  
		$unchangedQuestions=$db->query('DELETE FROM btFormQuestions WHERE bID=? AND questionSetId=? AND msqID IN (?)',$vals);			
	} 
	
	//Duplicate will run when copying a page with a block, or editing a block for the first time within a page version (before the save).
	function duplicate($newBID) { 
	
		$b=$this->getBlockObject(); 
		$c=$b->getBlockCollectionObject();	 
		 
		$db = Loader::db();
		$v = array($this->bID);
		$q = "select * from {$this->btTable} where bID = ? LIMIT 1";
		$r = $db->query($q, $v);
		$row = $r->fetchRow();
		
		//if the same block exists in multiple collections with the same questionSetID
		if(count($row)>0){ 
			$oldQuestionSetId=$row['questionSetId']; 
			
			//It should only generate a new question set id if the block is copied to a new page,
			//otherwise it will loose all of its answer sets (from all the people who've used the form on this page)
			$questionSetCIDs=$db->getCol("SELECT distinct cID FROM {$this->btTable} AS f, CollectionVersionBlocks AS cvb ".
						"WHERE f.bID=cvb.bID AND questionSetId=".intval($row['questionSetId']) );
			
			//this question set id is used on other pages, so make a new one for this page block 
			if( count( $questionSetCIDs ) >1 || !in_array( $c->cID, $questionSetCIDs ) ){ 
				$newQuestionSetId=time(); 
				$_POST['qsID']=$newQuestionSetId; 
			}else{
				//otherwise the question set id stays the same
				$newQuestionSetId=$row['questionSetId']; 
			}
			
			//duplicate survey block record 
			//with a new Block ID and a new Question 
			$v = array($newQuestionSetId,$row['surveyName'],$newBID,$row['thankyouMsg'],intval($row['notifyMeOnSubmission']),$row['recipientEmail'],$row['displayCaptcha'], $row['addFilesToSet']);
			$q = "insert into {$this->btTable} ( questionSetId, surveyName, bID,thankyouMsg,notifyMeOnSubmission,recipientEmail,displayCaptcha,addFilesToSet) values (?, ?, ?, ?, ?, ?, ?,?)";
			$result=$db->Execute($q, $v); 
			
			$rs=$db->query("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=$oldQuestionSetId AND bID=".intval($this->bID) );
			while( $row=$rs->fetchRow() ){
				$v=array($newQuestionSetId,intval($row['msqID']), intval($newBID), $row['question'],$row['inputType'],$row['options'],$row['position'],$row['width'],$row['height'],$row['required']);
				$sql= "INSERT INTO {$this->btQuestionsTablename} (questionSetId,msqID,bID,question,inputType,options,position,width,height,required) VALUES (?,?,?,?,?,?,?,?,?,?)";
				$db->Execute($sql, $v);
			}
			
			return $newQuestionSetId;
		}
		return 0;	
	}
	

	//users submits the completed survey
	function action_submit_form() { 
	
		$ip = Loader::helper('validation/ip');
		Loader::library("file/importer");
		
		if (!$ip->check()) {
			$this->set('invalidIP', $ip->getErrorMessage());			
			return;
		}	

		$txt = Loader::helper('text');
		$db = Loader::db();
		
		//question set id
		$qsID=intval($_POST['qsID']); 
		if($qsID==0)
			throw new Exception(t("Oops, something is wrong with the form you posted (it doesn't have a question set id)."));
			
		//get all questions for this question set
		$rows=$db->GetArray("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=? AND bID=? order by position asc, msqID", array( $qsID, intval($this->bID)));			

		// check captcha if activated
		if ($this->displayCaptcha) {
			$captcha = Loader::helper('validation/captcha');
			if (!$captcha->check()) {
				$errors['captcha'] = t("Incorrect captcha code");
				$_REQUEST['ccmCaptchaCode']='';
			}
		}
		
		//checked required fields
		foreach($rows as $row){
			if ($row['inputType']=='datetime'){
				if (!isset($datetime)) {
					$datetime = Loader::helper("form/date_time");
				}
				$translated = $datetime->translate('Question'.$row['msqID']);
				if ($translated) {
					$_POST['Question'.$row['msqID']] = $translated;
				}
			}
			if( intval($row['required'])==1 ){
				$notCompleted=0;
				if ($row['inputType'] == 'email') {
					if (!Loader::helper('validation/strings')->email($_POST['Question' . $row['msqID']])) {
						$errors['emails'] = t('You must enter a valid email address.');
					}
				}
				if($row['inputType']=='checkboxlist'){
					$answerFound=0;
					foreach($_POST as $key=>$val){
						if( strstr($key,'Question'.$row['msqID'].'_') && strlen($val) ){
							$answerFound=1;
						} 
					}
					if(!$answerFound) $notCompleted=1;
				}elseif($row['inputType']=='fileupload'){		
					if( !isset($_FILES['Question'.$row['msqID']]) || !is_uploaded_file($_FILES['Question'.$row['msqID']]['tmp_name']) )					
						$notCompleted=1;
				}elseif( !strlen(trim($_POST['Question'.$row['msqID']])) ){
					$notCompleted=1;
				} 
				if($notCompleted) $errors['CompleteRequired'] = t("Complete required fields *") ; 
			}
		}
		
		//try importing the file if everything else went ok	
		$tmpFileIds=array();	
		if(!count($errors))	foreach($rows as $row){
			if( $row['inputType']!='fileupload' ) continue;
			$questionName='Question'.$row['msqID']; 			
			if	( !intval($row['required']) && 
			   		( 
			   		!isset($_FILES[$questionName]['tmp_name']) || !is_uploaded_file($_FILES[$questionName]['tmp_name'])
			   		) 
				){
					continue;
			}
			$fi = new FileImporter();
			$resp = $fi->import($_FILES[$questionName]['tmp_name'], $_FILES[$questionName]['name']);
			if (!($resp instanceof FileVersion)) {
				switch($resp) {
					case FileImporter::E_FILE_INVALID_EXTENSION:
						$errors['fileupload'] = t('Invalid file extension.');
						break;
					case FileImporter::E_FILE_INVALID:
						$errors['fileupload'] = t('Invalid file.');
						break;
					
				}
			}else{
				$tmpFileIds[intval($row['msqID'])] = $resp->getFileID();
				if(intval($this->addFilesToSet)) {
					Loader::model('file_set');
					$fs = new FileSet();
					$fs = $fs->getByID($this->addFilesToSet);
					if($fs->getFileSetID()) {
						$fs->addFileToSet($resp);
					}
				}
			}
		}
		
		if(count($errors)){			
			$this->set('formResponse', t('Please correct the following errors:') );
			$this->set('errors',$errors);
		}else{ //no form errors			
			//save main survey record	
			$u = new User();
			$uID = 0;
			if ($u->isRegistered()) {
				$uID = $u->getUserID();
			}
			$q="insert into {$this->btAnswerSetTablename} (questionSetId, uID) values (?,?)";
			$db->query($q,array($qsID, $uID));
			$answerSetID=$db->Insert_ID();
			$this->lastAnswerSetId=$answerSetID;
			
			$questionAnswerPairs=array();

			if( strlen(FORM_BLOCK_SENDER_EMAIL)>1 && strstr(FORM_BLOCK_SENDER_EMAIL,'@') ){
				$formFormEmailAddress = FORM_BLOCK_SENDER_EMAIL;
			}else{
				$adminUserInfo=UserInfo::getByID(USER_SUPER_ID);
				$formFormEmailAddress = $adminUserInfo->getUserEmail();
			}
			$replyToEmailAddress = $formFormEmailAddress;
			//loop through each question and get the answers 
			foreach( $rows as $row ){	
				//save each answer
				if($row['inputType']=='checkboxlist'){
					$answer = Array();
					$answerLong="";
					$keys = array_keys($_POST);
					foreach ($keys as $key){
						if (strpos($key, 'Question'.$row['msqID'].'_') === 0){
							$answer[]=$txt->sanitize($_POST[$key]);
						}
					}
				}elseif($row['inputType']=='text'){
					$answerLong=$txt->sanitize($_POST['Question'.$row['msqID']]);
					$answer='';
				}elseif($row['inputType']=='fileupload'){
					$answerLong="";
					$answer=intval( $tmpFileIds[intval($row['msqID'])] );
				}elseif($row['inputType']=='url'){
					$answerLong="";
					$answer=$txt->sanitize($_POST['Question'.$row['msqID']]);
				}elseif($row['inputType']=='email'){
					$answerLong="";
					$answer=$txt->sanitize($_POST['Question'.$row['msqID']]);
					if(!empty($row['options'])) {
						$settings = unserialize($row['options']);
						if(is_array($settings) && array_key_exists('send_notification_from', $settings) && $settings['send_notification_from'] == 1) {
							$email = $txt->email($answer);
							if(!empty($email)) {
								$replyToEmailAddress = $email;
							}
						}
					}
				}elseif($row['inputType']=='telephone'){
					$answerLong="";
					$answer=$txt->sanitize($_POST['Question'.$row['msqID']]);
				}else{
					$answerLong="";
					$answer=$txt->sanitize($_POST['Question'.$row['msqID']]);
				}
				
				if( is_array($answer) ) 
					$answer=join(',',$answer);
									
				$questionAnswerPairs[$row['msqID']]['question']=$row['question'];
				$questionAnswerPairs[$row['msqID']]['answer']=$txt->sanitize( $answer.$answerLong );
				
				$v=array($row['msqID'],$answerSetID,$answer,$answerLong);
				$q="insert into {$this->btAnswersTablename} (msqID,asID,answer,answerLong) values (?,?,?,?)";
				$db->query($q,$v);
			}
			$foundSpam = false;
			
			$submittedData = '';
			foreach($questionAnswerPairs as $questionAnswerPair){
				$submittedData .= $questionAnswerPair['question']."\r\n".$questionAnswerPair['answer']."\r\n"."\r\n";
			} 
			$antispam = Loader::helper('validation/antispam');
			if (!$antispam->check($submittedData, 'form_block')) { 
				// found to be spam. We remove it
				$foundSpam = true;
				$q="delete from {$this->btAnswerSetTablename} where asID = ?";
				$v = array($this->lastAnswerSetId);
				$db->Execute($q, $v);
				$db->Execute("delete from {$this->btAnswersTablename} where asID = ?", array($this->lastAnswerSetId));
			}
			
			if(intval($this->notifyMeOnSubmission)>0 && !$foundSpam){	
				if ($this->sendEmailFrom !== false) {
					$formFormEmailAddress = $this->sendEmailFrom;
				} else if( strlen(FORM_BLOCK_SENDER_EMAIL)>1 && strstr(FORM_BLOCK_SENDER_EMAIL,'@') ){
					$formFormEmailAddress = FORM_BLOCK_SENDER_EMAIL;  
				}else{ 
					$adminUserInfo=UserInfo::getByID(USER_SUPER_ID);
					$formFormEmailAddress = $adminUserInfo->getUserEmail(); 
				}
				
				$mh = Loader::helper('mail');
				$mh->to( $this->recipientEmail ); 
				$mh->from( $formFormEmailAddress ); 
				$mh->replyto( $replyToEmailAddress ); 
				$mh->addParameter('formName', $this->surveyName);
				$mh->addParameter('questionSetId', $this->questionSetId);
				$mh->addParameter('questionAnswerPairs', $questionAnswerPairs); 
				$mh->load('block_form_submission');
				$mh->setSubject(t('%s Form Submission', $this->surveyName));
				//echo $mh->body.'<br>';
				@$mh->sendMail(); 
			} 
			
			if (!$this->noSubmitFormRedirect) {
				if ($this->redirectCID > 0) {
					$pg = Page::getByID($this->redirectCID);
					if (is_object($pg) && $pg->cID) {
						$this->redirect($pg->getCollectionPath());
					}
				}
				$c = Page::getCurrentPage();
				header("Location: ".Loader::helper('navigation')->getLinkToCollection($c, true)."?surveySuccess=1&qsid=".$this->questionSetId."#".$this->questionSetId);
				exit;
			}
		}
	}		
	
	function delete() { 
	
		$db = Loader::db();

		$deleteData['questionsIDs']=array();
		$deleteData['strandedAnswerSetIDs']=array();

		$miniSurvey=new MiniSurvey();
		$info=$miniSurvey->getMiniSurveyBlockInfo($this->bID);
		
		//get all answer sets
		$q = "SELECT asID FROM {$this->btAnswerSetTablename} WHERE questionSetId = ".intval($info['questionSetId']);
		$answerSetsRS = $db->query($q); 
 
		//delete the questions
		$deleteData['questionsIDs']=$db->getAll( "SELECT qID FROM {$this->btQuestionsTablename} WHERE questionSetId = ".intval($info['questionSetId']).' AND bID='.intval($this->bID) );
		foreach($deleteData['questionsIDs'] as $questionData)
			$db->query("DELETE FROM {$this->btQuestionsTablename} WHERE qID=".intval($questionData['qID']));			
		
		//delete left over answers
		$strandedAnswerIDs = $db->getAll('SELECT fa.aID FROM `btFormAnswers` AS fa LEFT JOIN btFormQuestions as fq ON fq.msqID=fa.msqID WHERE fq.msqID IS NULL');
		foreach($strandedAnswerIDs as $strandedAnswerIDs)
			$db->query('DELETE FROM `btFormAnswers` WHERE aID='.intval($strandedAnswer['aID']));
			
		//delete the left over answer sets
		$deleteData['strandedAnswerSetIDs'] = $db->getAll('SELECT aset.asID FROM btFormAnswerSet AS aset LEFT JOIN btFormAnswers AS fa ON aset.asID=fa.asID WHERE fa.asID IS NULL');
		foreach($deleteData['strandedAnswerSetIDs'] as $strandedAnswerSetIDs)
			$db->query('DELETE FROM btFormAnswerSet WHERE asID='.intval($strandedAnswerSetIDs['asID']));		
		
		//delete the form block		
		$q = "delete from {$this->btTable} where bID = '{$this->bID}'";
		$r = $db->query($q);		
		
		parent::delete();
		
		return $deleteData;
	}
}


