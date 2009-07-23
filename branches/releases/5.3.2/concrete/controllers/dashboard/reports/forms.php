<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('form');

class DashboardReportsFormsController extends Controller {

	private $pageSize=3; 

	public function view(){	
		if($_REQUEST['all']){
			$this->pageSize=100000; 
			$_REQUEST['page']=1;
		}
		$this->loadSurveyResponses();
	}

	public function excel(){ 
		$this->pageSize=0;
		$this->loadSurveyResponses();
		$textHelper = Loader::helper('text');
		
		$questionSet=$this->get('questionSet');
		$answerSets=$this->get('answerSets');
		$questions=$this->get('questions');	
		$surveys=$this->get('surveys');	 
		 
		$fileName=$textHelper->filterNonAlphaNum($surveys[$questionSet]['surveyName']);
		
		header("Content-Type: application/vnd.ms-excel");
		header("Cache-control: private");
		header("Pragma: public");
		$date = date('Ymd');
		header("Content-Disposition: inline; filename=".$fileName."_form_data_{$date}.xls"); 
		header("Content-Title: ".$surveys[$questionSet]['surveyName']." Form Data Output - Run on {$date}");		
		echo "<table>\r\n";
		$hasCBRow = false;
		foreach($questions as $questionId=>$question){ 
            if ($question['inputType'] == 'checkboxlist') {
				$hasCBRow = true;
			}
		}

		echo "<tr>";
		echo "\t\t<td ";
		if ($hasCBRow) {
			echo "rowspan=\"2\" valign='bottom'";
		}
		echo "><b>Submitted Date</b></td>\r\n";
		
		foreach($questions as $questionId=>$question){ 
            if ($question['inputType'] == 'checkboxlist')
            {
                $options = explode('%%', $question['options']);
			    echo "\t\t".'<td colspan="'.count($options).'"><b>'."\r\n";
            }
            else
            {
			    echo "\t\t<td ";
			    if ($hasCBRow) {
			    	echo "rowspan=\"2\" valign='bottom'>";
			    }
			    echo "<b>\r\n";
            }
			echo "\t\t\t".$questions[$questionId]['question']."\r\n";
			echo "\t\t</b></td>\r\n";			
		}	
		echo "</tr>";

		// checkbox row
		if ($hasCBRow) {
			echo "<tr>";
			foreach($questions as $questionId=>$question){ 
				if ($question['inputType'] == 'checkboxlist')
				{
					$options = explode('%%', $question['options']);
					foreach($options as $opt) {
						echo "<td><b>{$opt}</b></td>";
					}
				}
			}
			echo "</tr>";
		}
		
		foreach($answerSets as $answerSetId=>$answerSet){ 
			$questionNumber=0;
			$numQuestionsToShow=2;
			echo "\t<tr>\r\n";
			echo "\t\t<td>".$answerSet['created']."</td>\r\n";
			foreach($questions as $questionId=>$question){ 
				$questionNumber++;
                if ($question['inputType'] == 'checkboxlist'){
                    $options = explode('%%', $question['options']);
                    $subanswers = explode(',', $answerSet['answers'][$questionId]['answer']);
                    for ($i = 1; $i <= count($options); $i++)
                    {
				        echo "\t\t<td align='center'>\r\n";
                        if (in_array($options[$i-1], $subanswers))
				           // echo "\t\t\t".$options[$i-1]."\r\n";
				           echo "&#10004;";
                        else
				            echo "\t\t\t&nbsp;\r\n";
				        echo "\t\t</td>\r\n";
                    }
					
                }elseif($question['inputType']=='fileupload'){ 
					echo "\t\t<td>\r\n";
					$fID=intval($answerSet['answers'][$questionId]['answer']);
					$file=File::getByID($fID);
					if($fID && $file){
						$fileVersion=$file->getApprovedVersion();
						echo "\t\t\t".'<a href="'. BASE_URL . DIR_REL . $fileVersion->getRelativePath() .'">'.$fileVersion->getFileName().'</a>'."\r\n";
					}else{
						echo "\t\t\t".t('File not found')."\r\n";
					} 	
					echo "\t\t</td>\r\n";		
				}else{					
				    echo "\t\t<td>\r\n";
				    echo "\t\t\t".$answerSet['answers'][$questionId]['answer'].$answerSet['answers'][$questionId]['answerLong']."\r\n";					
				    echo "\t\t</td>\r\n";
                }
			}
			echo "\t</tr>\r\n";
		}
		echo "</table>\r\n";		
		die;
	}	

	private function loadSurveyResponses(){
		$c=$this->getCollectionObject();
		$db = Loader::db();
		$tempMiniSurvey = new MiniSurvey();
		$pageBase=DIR_REL.'/index.php?cID='.$c->getCollectionID();
		
		//load surveys
		$surveysRS=FormBlockStatistics::loadSurveys($tempMiniSurvey);
		
		//index surveys by question set id
		$surveys=array();
		while($survey=$surveysRS->fetchRow())
			$surveys[ $survey['questionSetId'] ] = $survey;	
	
		//load requested survey response
		if( strlen($_REQUEST['qsid'])>0 ){
			$questionSet=preg_replace('/[^[:alnum:]]/','',$_REQUEST['qsid']);
			
			//get Survey Questions
			$questionsRS=MiniSurvey::loadQuestions($questionSet);
			$questions=array();
			while( $question = $questionsRS->fetchRow() ){
				$questions[$question['msqID']]=$question;
			}
			
			//get Survey Answers
			$answerSetCount = MiniSurvey::getAnswerCount($questionSet);
			
			//pagination 
			$pageBaseSurvey=$pageBase.'&qsid='.$questionSet;
			$paginator=Loader::helper('pagination');
			$sortBy=$_REQUEST['sortBy'];
			$paginator->init( intval($_REQUEST['page']) ,$answerSetCount,$pageBaseSurvey.'&page=%pageNum%&sortBy='.$sortBy,$this->pageSize);
			
			if($this->pageSize>0)
				$limit=$paginator->getLIMIT();
			else $limit='';
			$answerSets = FormBlockStatistics::buildAnswerSetsArray( $questionSet, $sortBy, $limit ); 
		}
		$this->set('questions',$questions);		
		$this->set('answerSets',$answerSets);
		$this->set('paginator',$paginator);	
		$this->set('questionSet',$questionSet);
		$this->set('surveys',$surveys);  			
	}
}

?>