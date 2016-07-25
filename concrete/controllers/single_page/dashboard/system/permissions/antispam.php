<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Concrete\Core\Antispam\Library as SystemAntispamLibrary;
use GroupList;

class Antispam extends DashboardPageController
{
    public function view()
    {
        $list = SystemAntispamLibrary::getList();
        $libraries = array('' => t('** None Activated'));
        foreach ($list as $sc) {
            $libraries[$sc->getSystemAntispamLibraryHandle()] = $sc->getSystemAntispamLibraryName();
        }
        $scl = SystemAntispamLibrary::getActive();
        $this->set('activeLibrary', $scl);
        $this->set('libraries', $libraries);

        $db = Loader::db();
        $groups = array('-1' => t('** None Selected'));
        $gl = new GroupList();
        $gl->sortBy('gID', 'asc');
        $gl->includeAllGroups();
        $res = $gl->getResults();
        foreach ($res as $g) {
            $groups[$g->getGroupID()] = $g->getGroupDisplayName(false);
        }
        $this->groups = $groups;
        $this->set('groups', $groups);
        $this->set('whitelistGroup', Config::get('concrete.spam.whitelist_group'));
    }

    public function saved()
    {
        $this->set('message', t('Anti-spam settings saved.'));
        $this->view();
    }

    public function save()
    {
        $this->view();
        if (!isset($this->groups[$_POST['group_id']])) {
            $this->error->add('Invalid Group');

            return;
        }
        Config::save('concrete.spam.whitelist_group', $_POST['group_id']);
    }

    public function update_library()
    {
        $this->save();
        if (!$this->error->has() && Loader::helper("validation/token")->validate('update_library')) {
            if ($this->post('activeLibrary')) {
                $scl = SystemAntispamLibrary::getByHandle($this->post('activeLibrary'));
                if (is_object($scl)) {
                    $scl->activate();
                    Config::save('concrete.spam.notify_email', $this->post('ANTISPAM_NOTIFY_EMAIL'));
                    Config::save('concrete.log.spam', $this->post('ANTISPAM_LOG_SPAM'));
                    if ($scl->hasOptionsForm() && $this->post('ccm-submit-submit')) {
                        $controller = $scl->getController();
                        $controller->saveOptions($this->post());
                    }
                    $this->redirect('/dashboard/system/permissions/antispam', 'saved');
                } else {
                    $this->error->add(t('Invalid anti-spam library.'));
                }
            } else {
                SystemAntispamLibrary::deactivateAll();
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if ($this->error->has()) {
            $this->view();
        } else {
            $this->redirect('/dashboard/system/permissions/antispam', 'saved');
        }
    }
}
