<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Conversations_Spam extends Controller {

	public function view() {
		$db = Loader::db();
		$groups = array('-1'=>'** None Selected');
		$groupquery = $db->query('SELECT gID, gName from Groups');
		while ($group = $groupquery->fetchRow()) {
			$groups[$group['gID']] = $group['gName'];
		}
		$this->groups = $groups;
		$this->set('groups',$groups);
		$this->set('whitelistGroup',Config::get('CONVERSATION_SPAM_WHITELIST_GROUP'));
	}

	public function success() {
		$this->view();
		$this->set('message','Updated Whitelist Group.');
	}

	public function error() {
		$this->error = Loader::helper('validation/error');
		$this->error->add('Invalid Group.');
		$this->view();
		$this->set('error',$this->error);
	}

	public function save() {
		$this->view();
		if (!isset($this->groups[$_POST['group_id']])) {
			$this->redirect('/dashboard/system/conversations/spam/error');
			return;
		}
		Config::save('CONVERSATION_SPAM_WHITELIST_GROUP',$_POST['group_id']);
		$this->redirect('/dashboard/system/conversations/spam/success');
	}

}