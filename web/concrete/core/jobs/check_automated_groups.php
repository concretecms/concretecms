<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_CheckAutomatedGroups extends QueueableJob {

	public $jSupportsQueue = true;

	public function getJobName() {
		return t("Check Automated Groups");
	}
	
	public function getJobDescription() {
		return t("Automatically add users to groups and assign badges..");
	}

	public function start(Zend_Queue $q) {
		$db = Loader::db();
		$r = $db->Execute('select Users.uID from Users where uIsActive = 1 order by uID asc');
		while ($row = $r->FetchRow()) {
			$q->send($row['uID']);
		}
	}
	
	public function finish(Zend_Queue $q) {
		return t('Active users updated.');
	}

	public function processQueueItem(Zend_Queue_Message $msg) {
		$ux = User::getByUserID($msg->body);
		$groupControllers = Group::getAutomatedOnJobRunGroupControllers($ux);
		foreach($groupControllers as $ga) {
			if ($ga->check($ux)) {
				$ux->enterGroup($ga->getGroupObject());
			}
		}
	}


}