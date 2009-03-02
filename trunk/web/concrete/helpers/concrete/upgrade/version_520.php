<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class ConcreteUpgradeVersion520Helper {
	
	public function run() {
		$db = Loader::db();
		$tables = $db->MetaTables('TABLES');
		if (isset($tables['btFormQuestions'])) {
			try{
				$db->query('ALTER TABLE btFormQuestions CHANGE msqID msqID INT(11) UNSIGNED NOT NULL '); 
				$db->query('ALTER TABLE btFormQuestions DROP PRIMARY KEY'); 			
			}catch(Exception $e){ }
			try{
				$db->query('ALTER TABLE btFormQuestions ADD qID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY '); 			
			}catch(Exception $e){ } 
			
			//$db->CacheFlush();
			//$db->setDebug(true);
			$installResult = parent::install($path);  
			 
			//give all questions a bID 
			$questionsWithBIDs=$db->getAll('SELECT max(bID) AS bID, btForm.questionSetId AS qSetId FROM `btForm` GROUP BY questionSetId');
			foreach($questionsWithBIDs as $questionsWithBID){
				$vals=array( intval($questionsWithBID['bID']), intval($questionsWithBID['qSetId']) );
				$rs=$db->query('UPDATE btFormQuestions SET bID=? WHERE questionSetId=? AND bID=0',$vals);  
			}
		}
		
	}
	
}
		
	