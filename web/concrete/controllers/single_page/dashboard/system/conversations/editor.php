<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;

use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Concrete\Core\Routing\Redirect;

class Editor extends DashboardPageController
{
    public function view()
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

    public function success()
    {
        $this->set('message', t('The active editor has been updated.'));
        $this->view();
    }

    public function error()
    {
        $this->error->add(t('Invalid editor handle.'));
        $this->view();
    }

    public function save()
    {
        $this->view();
        $active = $this->post('activeEditor');
        $db = Loader::db();
        if (!isset($this->editors[$active])) {
            $this->redirect('/dashboard/system/conversations/editor/error');

            return;
        }
        $db->executeQuery('UPDATE ConversationEditors SET cnvEditorIsActive=0');
        $db->executeQuery('UPDATE ConversationEditors SET cnvEditorIsActive=1 WHERE cnvEditorHandle=?', array($active));
        $this->redirect('/dashboard/system/conversations/editor', 'success');
    }
}
