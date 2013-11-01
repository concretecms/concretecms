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
class Concrete5_Controller_Block_FormMinisurvey {

		public $btTable = 'btForm';
		public $btQuestionsTablename = 'btFormQuestions';
		public $btAnswerSetTablename = 'btFormAnswerSet';
		public $btAnswersTablename = 'btFormAnswers'; 	
		
		public $lastSavedMsqID=0;
		public $lastSavedqID=0;

		function __construct(){
			$db = Loader::db();
			$this->db=$db;
		}

		function addEditQuestion($values,$withOutput=1){
			$jsonVals=array();
			$values['options']=str_replace(array("\r","\n"),'%%',$values['options']); 
			if(strtolower($values['inputType'])=='undefined')  $values['inputType']='field';
			
			//set question set id, or create a new one if none exists
			if(intval($values['qsID'])==0) $values['qsID']=time(); 
			
			//validation
			if( strlen($values['question'])==0 || strlen($values['inputType'])==0  || $values['inputType']=='null' ){
				//complete required fields
				$jsonVals['success']=0;
				$jsonVals['noRequired']=1;
			}else{
				
				if( intval($values['msqID']) ){
					$jsonVals['mode']='"Edit"';
					
					//questions that are edited are given a placeholder row in btFormQuestions with bID=0, until a bID is assign on block update
					$pendingEditExists = $this->db->getOne( "select count(*) as total from btFormQuestions where bID=0 AND msqID=".intval($values['msqID']) );
					
					//hideQID tells the interface to hide the old version of the question in the meantime
					$vals=array( intval($values['msqID'])); 		
					$jsonVals['hideQID']=intval($this->db->GetOne("SELECT MAX(qID) FROM btFormQuestions WHERE bID!=0 AND msqID=?",$vals));	
				}else{
					$jsonVals['mode']='"Add"';
				}

				//see if the 'send notification from' checkbox is checked and save this to the options field
				if($values['inputType'] == 'email') {
					$options = array();
					if(array_key_exists('send_notification_from', $values) && $values['send_notification_from'] == 1) {
						$options['send_notification_from'] = 1;
					} else {
						$options['send_notification_from'] = 0;
					}
					$values['options'] = serialize($options);
				}
				if( $pendingEditExists ){ 
					$width = $height = 0;
					if ($values['inputType'] == 'text'){
						$width  = $this->limitRange(intval($values['width']), 20, 500);
						$height = $this->limitRange(intval($values['height']), 1, 100); 
					}
					$dataValues=array(intval($values['qsID']), trim($values['question']), $values['inputType'],
								      $values['options'], intval($values['position']), $width, $height, intval($values['required']), intval($values['msqID']) );			
					$sql='UPDATE btFormQuestions SET questionSetId=?, question=?, inputType=?, options=?, position=?, width=?, height=?, required=? WHERE msqID=? AND bID=0';					
				}else{ 
					if( !isset($values['position']) ) $values['position']=1000;
					if(!intval($values['msqID']))
						$values['msqID']=intval($this->db->GetOne("SELECT MAX(msqID) FROM btFormQuestions")+1); 
					$dataValues=array($values['msqID'],intval($values['qsID']), trim($values['question']), $values['inputType'],
								     $values['options'], intval($values['position']), intval($values['width']), intval($values['height']), intval($values['required']) );			
					$sql='INSERT INTO btFormQuestions (msqID,questionSetId,question,inputType,options,position,width,height,required) VALUES (?,?,?,?,?,?,?,?,?)'; 
				}
				$result=$this->db->query($sql,$dataValues);  
				$this->lastSavedMsqID=intval($values['msqID']);	
				$this->lastSavedqID=intval($this->db->GetOne("SELECT MAX(qID) FROM btFormQuestions WHERE bID=0 AND msqID=?", array($values['msqID']) ));
				$jsonVals['qID']=$this->lastSavedqID;
				$jsonVals['success']=1;
			}
			
			$jsonVals['qsID']=$values['qsID'];
			$jsonVals['msqID']=intval($values['msqID']);
			//create json response object
			$jsonPairs=array();
			foreach($jsonVals as $key=>$val) $jsonPairs[]=$key.':'.$val;
			if($withOutput) echo '{'.join(',',$jsonPairs).'}';
		}
		
		function getQuestionInfo($qsID,$qID){
			$questionRS=$this->db->query('SELECT * FROM btFormQuestions WHERE questionSetId='.intval($qsID).' AND qID='.intval($qID).' LIMIT 1' );
			$questionRow=$questionRS->fetchRow();
			$jsonPairs=array();
			foreach($questionRow as $key=>$val){
				if($key=='options') {
					$key='optionVals';
					if($questionRow['inputType'] == 'email') {
						$options = unserialize($val);
						if (is_array($options)) {
							foreach($options as $o_key => $o_val) {
								$val = $o_key."::".$o_val.";";
							}
						}
					}
				}

				$jsonPairs[]=$key.':"'.str_replace(array("\r","\n"),'%%',addslashes($val)).'"';
			}
			echo '{'.join(',',$jsonPairs).'}';
		}

		function deleteQuestion($qsID,$msqID){
			$sql='DELETE FROM btFormQuestions WHERE questionSetId='.intval($qsID).' AND msqID='.intval($msqID).' AND bID=0';
			$this->db->query($sql,$dataValues);
		} 
		
		function loadQuestions($qsID, $bID=0, $showPending=0 ){
			$db = Loader::db();
			if( intval($bID) ){
				$bIDClause=' AND ( bID='.intval($bID).' ';			
				if( $showPending ) 
					 $bIDClause.=' OR bID=0) ';	
				else $bIDClause.=' ) ';	
			}
			return $db->query('SELECT * FROM btFormQuestions WHERE questionSetId='.intval($qsID).' '.$bIDClause.' ORDER BY position, msqID');
		}
		
		static function getAnswerCount($qsID){
			$db = Loader::db();
			return $db->getOne( 'SELECT count(*) FROM btFormAnswerSet WHERE questionSetId='.intval($qsID) );
		}		
		
		function loadSurvey( $qsID, $showEdit=false, $bID=0, $hideQIDs=array(), $showPending=0 ){
		
			//loading questions	
			$questionsRS=$this->loadQuestions( $qsID, $bID, $showPending);
		
		
			if(!$showEdit){
				echo '<table class="formBlockSurveyTable">';					
				while( $questionRow=$questionsRS->fetchRow() ){	
				
					if( in_array($questionRow['qID'], $hideQIDs) ) continue;
					
					// this special view logic for the checkbox list isn't doing it for me
					/*
					if ($questionRow['inputType'] == 'checkboxlist' && strpos($questionRow['options'], '%%') === false){
						echo '<tr>
						        <td valign="top" colspan="2" class="question">
						          <div class="checkboxItem">
						            <div class="checkboxPair">'.$this->loadInputType($questionRow,$showEdit).$questionRow['question'].'</div>
						          </div>
						        </td>
						      </tr>';
					} else { */
						$requiredSymbol=($questionRow['required'])?'&nbsp;<span class="required">*</span>':'';
						echo '<tr>
						        <td valign="top" class="question"><label for="Question'.intval($questionRow['msqID']).'">'.$questionRow['question'].''.$requiredSymbol.'</label></td>
						        <td valign="top">'.$this->loadInputType($questionRow, $showEdit).'</td>
						      </tr>';
					//}
				}			
				$surveyBlockInfo = $this->getMiniSurveyBlockInfoByQuestionId($qsID,intval($bID));
				
				if($surveyBlockInfo['displayCaptcha']) {
				  echo '<tr><td colspan="2">';
   				$captcha = Loader::helper('validation/captcha');				
				echo $captcha->label();
   				echo '</td></tr><tr><td>&nbsp;</td><td>';
   				
   				$captcha->showInput();
   				$captcha->display();
   
   				//echo isset($errors['captcha'])?'<span class="error">' . $errors['captcha'] . '</span>':'';
				  echo '</td></tr>';
   				
   			}
			
				echo '<tr><td>&nbsp;</td><td><input class="formBlockSubmitButton ccm-input-button" name="Submit" type="submit" value="'.t('Submit').'" /></td></tr>';
				echo '</table>';
				
			}else{
			
			
				echo '<div id="miniSurveyTableWrap"><div id="miniSurveyPreviewTable" class="miniSurveyTable">';					
				while( $questionRow=$questionsRS->fetchRow() ){	 
				
					if( in_array($questionRow['qID'], $hideQIDs) ) continue;
				
					$requiredSymbol=($questionRow['required'])?'<span class="required">*</span>':'';				
					?>
					<div id="miniSurveyQuestionRow<?php echo $questionRow['msqID']?>" class="miniSurveyQuestionRow">
						<div class="miniSurveyQuestion"><?php echo $questionRow['question'].' '.$requiredSymbol?></div>
						<?php  /* <div class="miniSurveyResponse"><?php echo $this->loadInputType($questionRow,$showEdit)?></div> */ ?>
						<div class="miniSurveyOptions">
							<div style="float:right">
								<a href="javascript:void(0)" onclick="miniSurvey.moveUp(this,<?php echo $questionRow['msqID']?>);return false" class="moveUpLink"></a> 
								<a href="javascript:void(0)" onclick="miniSurvey.moveDown(this,<?php echo $questionRow['msqID']?>);return false" class="moveDownLink"></a>						  
							</div>						
							<a href="javascript:void(0)" onclick="miniSurvey.reloadQuestion(<?=intval($questionRow['qID']) ?>);return false"><?php echo t('edit')?></a> &nbsp;&nbsp; 
							<a href="javascript:void(0)" onclick="miniSurvey.deleteQuestion(this,<?=intval($questionRow['msqID']) ?>,<?=intval($questionRow['qID'])?>);return false"><?= t('remove')?></a>
						</div>
						<div class="miniSurveySpacer"></div>
					</div>
				<?php  }			 
				echo '</div></div>';
			}
		}
		
		function loadInputType($questionData,$showEdit){
			$options=explode('%%',$questionData['options']);
			$msqID=intval($questionData['msqID']);
			$datetime = loader::helper('form/date_time');
			switch($questionData['inputType']){			
				case 'checkboxlist': 
					// this is looking really crappy so i'm going to make it behave the same way all the time - andrew
					/*
					if (count($options) == 1){
						if(strlen(trim($options[0]))==0) continue;
						$checked=($_REQUEST['Question'.$msqID.'_0']==trim($options[0]))?'checked':'';
						$html.= '<input name="Question'.$msqID.'_0" type="checkbox" value="'.trim($options[0]).'" '.$checked.' />';
					}else{
					*/
					$html.= '<div class="checkboxList">'."\r\n";
					for ($i = 0; $i < count($options); $i++) {
						if(strlen(trim($options[$i]))==0) continue;
						$checked=($_REQUEST['Question'.$msqID.'_'.$i]==trim($options[$i]))?'checked':'';
						$html.= '  <div class="checkboxPair"><input name="Question'.$msqID.'_'.$i.'" type="checkbox" value="'.trim($options[$i]).'" '.$checked.' />&nbsp;'.$options[$i].'</div>'."\r\n";
					}
					$html.= '</div>';
					//}
					return $html;

				case 'select':
					if($this->frontEndMode){
						$selected=(!$_REQUEST['Question'.$msqID])?'selected="selected"':'';
						$html.= '<option value="" '.$selected.'>----</option>';					
					}
					foreach($options as $option){
						$checked=($_REQUEST['Question'.$msqID]==trim($option))?'selected="selected"':'';
						$html.= '<option '.$checked.'>'.trim($option).'</option>';
					}
					return '<select name="Question'.$msqID.'" id="Question'.$msqID.'" >'.$html.'</select>';
								
				case 'radios':
					foreach($options as $option){
						if(strlen(trim($option))==0) continue;
						$checked=($_REQUEST['Question'.$msqID]==trim($option))?'checked':'';
						$html.= '<div class="radioPair"><input name="Question'.$msqID.'" type="radio" value="'.trim($option).'" '.$checked.' />&nbsp;'.$option.'</div>';
					}
					return $html;
					
				case 'fileupload': 
					$html='<input type="file" name="Question'.$msqID.'" id="Question'.$msqID.'" />'; 				
					return $html;
					
				case 'text':
					$val=($_REQUEST['Question'.$msqID])?Loader::helper('text')->entities($_REQUEST['Question'.$msqID]):'';
					return '<textarea name="Question'.$msqID.'" id="Question'.$msqID.'" cols="'.$questionData['width'].'" rows="'.$questionData['height'].'">'.$val.'</textarea>';
				case 'url':
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return '<input name="Question'.$msqID.'" id="Question'.$msqID.'" type="url" value="'.stripslashes(htmlspecialchars($val)).'" />';
				case 'telephone':
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return '<input name="Question'.$msqID.'" id="Question'.$msqID.'" type="tel" value="'.stripslashes(htmlspecialchars($val)).'" />';
				case 'email':
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return '<input name="Question'.$msqID.'" id="Question'.$msqID.'" type="email" value="'.stripslashes(htmlspecialchars($val)).'" />';	
				case 'date':
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return $datetime->date('Question'.$msqID,($val!==''?$val:'now'));
				case 'datetime':
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return $datetime->datetime('Question'.$msqID,$val);
				case 'field':
				default:
					$val=($_REQUEST['Question'.$msqID])?$_REQUEST['Question'.$msqID]:'';
					return '<input name="Question'.$msqID.'" id="Question'.$msqID.'" type="text" value="'.stripslashes(htmlspecialchars($val)).'" />';
			}
		}
		
		function getMiniSurveyBlockInfo($bID){
			$rs=$this->db->query('SELECT * FROM btForm WHERE bID='.intval($bID).' LIMIT 1' );
			return $rs->fetchRow();
		}
		
		function getMiniSurveyBlockInfoByQuestionId($qsID,$bID=0){
			$sql='SELECT * FROM btForm WHERE questionSetId='.intval($qsID);
			if(intval($bID)>0) $sql.=' AND bID='.$bID;
			$sql.=' LIMIT 1'; 
			$rs=$this->db->query( $sql );
			return $rs->fetchRow();
		}		
		
		function reorderQuestions($qsID=0,$qIDs){
			$qIDs=explode(',',$qIDs);
			if(!is_array($qIDs)) $qIDs=array($qIDs);
			$positionNum=0;
			foreach($qIDs as $qID){
				$vals=array( $positionNum,intval($qID), intval($qsID) );
				$sql='UPDATE btFormQuestions SET position=? WHERE msqID=? AND questionSetId=?';
				$rs=$this->db->query($sql,$vals);
				$positionNum++;
			}
		}		

		function limitRange($val, $min, $max){
			$val = ($val < $min) ? $min : $val;
			$val = ($val > $max) ? $max : $val;
			return $val;
		}
				
		//Run on Form block edit
		static function questionCleanup( $qsID=0, $bID=0 ){
			$db = Loader::db();
		
			//First make sure that the bID column has been set for this questionSetId (for backwards compatibility)
			$vals=array( intval($qsID) ); 
			$questionsWithBIDs=$db->getOne('SELECT count(*) FROM btFormQuestions WHERE bID!=0 AND questionSetId=? ',$vals);
			
			//form block was just upgraded, so set the bID column
			if(!$questionsWithBIDs){ 
				$vals=array( intval($bID), intval($qsID) );  
				$rs=$db->query('UPDATE btFormQuestions SET bID=? WHERE bID=0 AND questionSetId=?',$vals);
				return; 
			} 			
			
			//Then remove all temp/placeholder questions for this questionSetId that haven't been assigned to a block
			$vals=array( intval($qsID) );  
			$rs=$db->query('DELETE FROM btFormQuestions WHERE bID=0 AND questionSetId=?',$vals);			
		}
}	
