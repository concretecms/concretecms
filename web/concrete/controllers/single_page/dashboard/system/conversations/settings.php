<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Settings extends DashboardPageController {

	public function view() {
		$helperFile = Loader::helper('concrete/file');
		$fileAccessFileTypes = Config::get('conversations.files.allowed_types');
		//is nothing's been defined, display the constant value
		if (!$fileAccessFileTypes) {
			$fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions(Config::get('concrete.upload.extensions'));
		}
		else {
			$fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($fileAccessFileTypes);
		}
		$this->set('file_access_file_types', $fileAccessFileTypes);
		$this->set('maxFileSizeGuest', Config::get('conversations.files.guest.max_size'));
		$this->set('maxFileSizeRegistered', Config::get('conversations.files.registered.max_size'));
		$this->set('maxFilesGuest', Config::get('conversations.files.guest.max'));
		$this->set('maxFilesRegistered', Config::get('conversations.files.registered.max'));
		$this->set('fileExtensions', implode(',', $fileAccessFileTypes));
        $this->set('attachmentsEnabled', intval(Config::get('conversations.attachments_enabled')));
        $this->loadEditors();
	}

    protected function loadEditors()
    {
        $db = Loader::db();
        $q = $db->executeQuery('SELECT * FROM ConversationEditors');
        $editors = array();
        $active = false;
        while ($row = $q->fetch()) {
            if ($row['cnvEditorIsActive'] == 1) {
                $active = $row['cnvEditorHandle'];
            }
            $editors[$row['cnvEditorHandle']] = tc('ConversationEditorName', $row['cnvEditorName']);
        }
        $q->closeCursor();
        if (!$active) {
            $active = array_pop(array_reverse($editors));
        }
        $this->set('active', $active);
        $this->set('editors', $editors);
        $this->editors = $editors;
    }

    protected function saveEditors()
    {
        $this->loadEditors();
        $active = $this->post('activeEditor');
        $db = Loader::db();
        if (!isset($this->editors[$active])) {
            $this->redirect('/dashboard/system/conversations/editor/error');
            return;
        }
        $db->executeQuery('UPDATE ConversationEditors SET cnvEditorIsActive=0');
        $db->executeQuery('UPDATE ConversationEditors SET cnvEditorIsActive=1 WHERE cnvEditorHandle=?', array($active));
    }

    public function success() {
		$this->view();
		$this->set('message', t('Updated conversations settings.'));
	}

	public function save() {
		$helper_file = Loader::helper('concrete/file');
        Config::save('conversations.files.guest.max_size', intval($this->post('maxFileSizeGuest')));
        Config::save('conversations.files.registered.max_size', intval($this->post('maxFileSizeRegistered')));
        Config::save('conversations.files.guest.max', intval($this->post('maxFilesGuest')));
        Config::save('conversations.files.registered.max', intval($this->post('maxFilesRegistered')));
        Config::save('conversations.attachments_enabled', !!$this->post('attachmentsEnabled'));
		if ($this->post('fileExtensions')){
			$types = preg_split('{,}',$this->post('fileExtensions'),null,PREG_SPLIT_NO_EMPTY);
			$types = $helper_file->serializeUploadFileExtensions($types);
			Config::save('conversations.files.allowed_types',$types);
		}
        $this->saveEditors();
		$this->success();
	}

}
