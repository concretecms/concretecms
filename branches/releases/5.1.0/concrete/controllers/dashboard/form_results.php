<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('form');

class DashboardFormResultsController extends Controller {

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
		echo "\t\t<td><b>Submitted Date</b></td>\r\n";
		foreach($questions as $questionId=>$question){ 
			echo "\t\t<td><b>\r\n";
			echo "\t\t\t".$questions[$questionId]['question']."\r\n";
			echo "\t\t</b></td>\r\n";			
		}		
		foreach($answerSets as $answerSetId=>$answerSet){ 
			$questionNumber=0;
			$numQuestionsToShow=2;
			echo "\t<tr>\r\n";
			echo "\t\t<td>".$answerSet['created']."</td>\r\n";
			foreach($questions as $questionId=>$question){ 
				$questionNumber++;
				echo "\t\t<td>\r\n";
				echo "\t\t\t".$answerSet['answers'][$questionId]['answer'].$answerSet['answers'][$questionId]['answerLong']."\r\n";
				echo "\t\t</td>\r\n";
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