<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;

use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Page\Controller\DashboardPageController;
use Core;

class Settings extends DashboardPageController
{
    public function view()
    {
        $config = Core::make('config');
        $helperFile = Core::make('helper/concrete/file');
        $fileAccessFileTypes = $config->get('conversations.files.allowed_types');
        //is nothing's been defined, display the constant value
        if (!$fileAccessFileTypes) {
            $fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($config->get('concrete.upload.extensions'));
        } else {
            $fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($fileAccessFileTypes);
        }
        $this->set('file_access_file_types', $fileAccessFileTypes);
        $this->set('maxFileSizeGuest', $config->get('conversations.files.guest.max_size'));
        $this->set('maxFileSizeRegistered', $config->get('conversations.files.registered.max_size'));
        $this->set('maxFilesGuest', $config->get('conversations.files.guest.max'));
        $this->set('maxFilesRegistered', $config->get('conversations.files.registered.max'));
        $this->set('fileExtensions', implode(',', $fileAccessFileTypes));
        $this->set('attachmentsEnabled', intval($config->get('conversations.attachments_enabled')));
        $this->loadEditors();
        $this->set('notificationUsers', Conversation::getDefaultSubscribedUsers());
        $this->set('subscriptionEnabled', intval($config->get('conversations.subscription_enabled')));
    }

    protected function loadEditors()
    {
        $db = Core::make('Concrete\Core\Database\Connection\Connection');
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
        $db = Core::make('Concrete\Core\Database\Connection\Connection');
        if (!isset($this->editors[$active])) {
            $this->redirect('/dashboard/system/conversations/editor/error');

            return;
        }
        $db->executeQuery('UPDATE ConversationEditors SET cnvEditorIsActive=0');
        $db->executeQuery('UPDATE ConversationEditors SET cnvEditorIsActive=1 WHERE cnvEditorHandle=?', array($active));
    }

    public function success()
    {
        $this->view();
        $this->set('message', t('Updated conversations settings.'));
    }

    public function save()
    {
        $config = Core::make('config');
        if (Core::make('token')->validate('conversations.settings.save')) {
            $helper_file = Core::make('helper/concrete/file');
            $config->save('conversations.files.guest.max_size', intval($this->post('maxFileSizeGuest')));
            $config->save('conversations.files.registered.max_size', intval($this->post('maxFileSizeRegistered')));
            $config->save('conversations.files.guest.max', intval($this->post('maxFilesGuest')));
            $config->save('conversations.files.registered.max', intval($this->post('maxFilesRegistered')));
            $config->save('conversations.attachments_enabled', (bool) $this->post('attachmentsEnabled'));
            $config->save('conversations.subscription_enabled', (bool) $this->post('subscriptionEnabled'));
            $users = array();
            if (is_array($this->post('defaultUsers'))) {
                foreach ($this->post('defaultUsers') as $uID) {
                    $ui = \UserInfo::getByID($uID);
                    if (is_object($ui)) {
                        $users[] = $ui;
                    }
                }
            }
            Conversation::setDefaultSubscribedUsers($users);
            if ($this->post('fileExtensions')) {
                $types = preg_split('{,}', $this->post('fileExtensions'), null, PREG_SPLIT_NO_EMPTY);
                $types = $helper_file->serializeUploadFileExtensions($types);
                $config->save('conversations.files.allowed_types', $types);
            }
            $this->saveEditors();
            $this->success();
        } else {
            $this->error->add('Invalid Token.');
            $this->view();
        }
    }
}
