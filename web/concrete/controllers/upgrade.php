<?php
namespace Concrete\Controller;

use Concrete\Core\Updater\Update;
use View;
use Concrete\Controller\Backend\UserInterface as BackendUserInterfaceController;
use Config;

class Upgrade extends BackendUserInterfaceController
{
    public function canAccess()
    {
        if (!Config::get('concrete.updates.enable_permissions_protection')) {
            return true; // we have turned this off temporarily which means anyone even non-logged-in users can run update.
        }

        $p = new \Permissions();

        return $p->canUpgrade();
    }

    public function on_start()
    {
        parent::on_start();

        $this->view = new View('/frontend/upgrade');
        $this->setTheme('concrete');

        $this->siteVersion = \Config::get('concrete.version_installed');
        $this->checkSecurity();
        \Cache::disableAll();
    }

    public function checkSecurity()
    {
        $fh = \Loader::helper('file');
        $updates = $fh->getDirectoryContents(DIR_CORE_UPDATES);
        foreach ($updates as $upd) {
            if (is_dir(DIR_CORE_UPDATES . '/' . $upd) && is_writable(DIR_CORE_UPDATES . '/' . $upd)) {
                if (file_exists(DIR_CORE_UPDATES . '/' . $upd . '/' . DISPATCHER_FILENAME) && is_writable(
                        DIR_CORE_UPDATES . '/' . $upd . '/' . DISPATCHER_FILENAME)
                ) {
                    unlink(DIR_CORE_UPDATES . '/' . $upd . '/' . DISPATCHER_FILENAME);
                }
                if (!file_exists(DIR_CORE_UPDATES . '/' . $upd . '/index.html')) {
                    touch(DIR_CORE_UPDATES . '/' . $upd . '/index.html');
                }
            }
        }
    }

    public function submit()
    {
        if ($this->validateAction()) {
            try {
                Update::updateToCurrentVersion();
                $this->set('success', t('Upgrade to <b>%s</b> complete!', APP_VERSION));
            } catch (\Exception $e) {
                $this->set('error', $e);
            }
        }
    }

    public function view()
    {
        $sav = $this->siteVersion;

        if (!$sav) {
            $message = t('Unable to determine your current version of concrete5. Upgrading cannot continue.');
        } elseif ($this->request->query->get('force', 0) == 1) {
            $this->set('do_upgrade', true);
        } else {
            if (version_compare($sav, APP_VERSION, '>')) {
                $message = t('Upgrading from <b>%s</b>', $sav) . '<br/>';
                $message .= t('Upgrading to <b>%s</b>', APP_VERSION) . '<br/><br/>';
                $message .= t(
                    'Your current website uses a version of concrete5 greater than this one. You cannot upgrade.');
            } else {
                if (version_compare($sav, APP_VERSION, '=')) {
                    $message = t(
                        'Your site is already up to date! The current version of concrete5 is <b>%s</b>.',
                        APP_VERSION);
                } else {
                    $message = '';
                    $message .= t('Upgrading from <b>%s</b>', $sav) . '<br/>';
                    $message .= t('Upgrading to <b>%s</b>', APP_VERSION) . '<br/><br/>';
                    $this->set('do_upgrade', true);
                }
            }
        }
        $this->set('status', $message);
    }
}
