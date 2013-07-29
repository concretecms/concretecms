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
class Concrete5_Controller_Block_FormStatistics {

	public static function getTotalSubmissions($date = null) {
		$db = Loader::db();
		if ($date != null) {
			return $db->GetOne("select count(asID) from btFormAnswerSet where DATE_FORMAT(created, '%Y-%m-%d') = ?", array($date));
		} else {
			return $db->GetOne("select count(asID) from btFormAnswerSet");
		}

	}
	
	public static function loadSurveys($MiniSurvey){  
		$db = Loader::db();
		return $db->query('SELECT s.* FROM '.$MiniSurvey->btTable.' AS s, Blocks AS b, BlockTypes AS bt 
			WHERE s.bID=b.bID AND b.btID=bt.btID AND bt.btHandle="form" AND EXISTS (
				SELECT 1 FROM CollectionVersionBlocks cvb
				INNER JOIN CollectionVersions cv ON cvb.cID=cv.cID AND cvb.cvID=cv.cvID	
				INNER JOIN Pages p ON cv.cID = p.cID
				WHERE cvb.bID=s.bID AND p.cIsActive=1 AND cv.cvIsApproved=1	
			)' );
	}
	
	public static $sortChoices=array('newest'=>'created DESC','chrono'=>'created');
	
	public static function buildAnswerSetsArray( $questionSet, $orderBy='', $limit='' ){
		$db = Loader::db();
		
		if( strlen(trim($limit))>0 && !strstr(strtolower($limit),'limit')  )
			$limit=' LIMIT '.$limit;
			
		if( strlen(trim($orderBy))>0 && array_key_exists($orderBy, self::$sortChoices) ){
			 $orderBySQL=self::$sortChoices[$orderBy];
		}else $orderBySQL=self::$sortChoices['newest'];
		
		//get answers sets
		$sql='SELECT * FROM btFormAnswerSet AS aSet '.
			 'WHERE aSet.questionSetId='.$questionSet.' ORDER BY '.$orderBySQL.' '.$limit;
		$answerSetsRS=$db->query($sql);
		//load answers into a nicer multi-dimensional array
		$answerSets=array();
		$answerSetIds=array(0);
		while( $answer = $answerSetsRS->fetchRow() ){
			//answer set id - question id
			$answerSets[$answer['asID']]=$answer;
			$answerSetIds[]=$answer['asID'];
		}		
		
		//get answers
		$sql='SELECT * FROM btFormAnswers AS a WHERE a.asID IN ('.join(',',$answerSetIds).')';
		$answersRS=$db->query($sql);
		
		//load answers into a nicer multi-dimensional array 
		while( $answer = $answersRS->fetchRow() ){
			//answer set id - question id
			$answerSets[$answer['asID']]['answers'][$answer['msqID']]=$answer;
		}
		return $answerSets;
	}
}


