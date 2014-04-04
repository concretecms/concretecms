<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_System_Conversations_Editor extends DashboardController {

	public function view() {
		$db = Loader::db();
		$q = $db->query('SELECT * FROM ConversationEditors');
		$editors = array();
		$active = false;
		while ($row = $q->fetchRow()) {
			if ($row['cnvEditorIsActive'] == 1) $active = $row['cnvEditorHandle'];
			$editors[$row['cnvEditorHandle']] = $row['cnvEditorName'];
		}
		if (!$active) $active = array_pop(array_reverse($editors));
		$this->set('active',$active);
		$this->set('editors',$editors);
		$this->editors = $editors;
	}

	public function success() {
		$this->view();
		$this->set('message','Updated active editor.');
	}

	public function error() {
		$this->error = Loader::helper('validation/error');
		$this->error->add('Invalid Editor Handle.');
		$this->view();
		$this->set('error',$this->error);
	}

	public function save() {
		$this->view();
		$active = $this->post('activeEditor');
		$db = Loader::db();

		if (!isset($this->editors[$active])) {
			$this->redirect('/dashboard/system/conversations/editor/error');
			return;
		}
		$db->execute('UPDATE ConversationEditors SET cnvEditorIsActive=0');
		$db->execute('UPDATE ConversationEditors SET cnvEditorIsActive=1 WHERE cnvEditorHandle=?',array($active));
		$this->redirect('/dashboard/system/conversations/editor/success');
	}

}