<?php 

namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Editor extends DashboardPageController {

	public function view() {
		$db = Loader::db();
		$q = $db->query('SELECT * FROM ConversationEditors');
		$editors = array();
		$active = false;
		while ($row = $q->fetchRow()) {
			if ($row['cnvEditorIsActive'] == 1) $active = $row['cnvEditorHandle'];
			$editors[$row['cnvEditorHandle']] = tc('ConversationEditorName', $row['cnvEditorName']);
		}
		if (!$active) $active = array_pop(array_reverse($editors));
		$this->set('active',$active);
		$this->set('editors',$editors);
		$this->editors = $editors;
	}

	public function success() {
		$this->view();
		$this->set('message', t('The active editor has been updated.'));
	}

	public function error() {
		$this->error = Loader::helper('validation/error');
		$this->error->add(t('Invalid editor handle.'));
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
		$this->redirect('/dashboard/system/conversations/editor', 'success');
	}

}