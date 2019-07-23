<?php

namespace Concrete\Controller;

use Concrete\Controller\Backend\UserInterface as BackendUserInterfaceController;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Permission\Checker;
use Concrete\Core\System\Mutex\MutexInterface;
use Concrete\Core\Updater\Update;
use Exception;
use View;

class Upgrade extends BackendUserInterfaceController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        if (!$this->app->make('config')->get('concrete.updates.enable_permissions_protection')) {
            return true; // we have turned this off temporarily which means anyone even non-logged-in users can run update.
        }

        $p = new Checker();

        return $p->canUpgrade();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::on_start()
     */
    public function on_start()
    {
        parent::on_start();

        $this->view = new View('/frontend/upgrade');
        $this->setTheme('concrete');

        $this->siteVersion = $this->app->make('config')->get('concrete.version_installed');
        $this->checkSecurity();
        Cache::disableAll();
    }

    public function checkSecurity()
    {
        $fh = $this->app->make('helper/file');
        $updates = $fh->getDirectoryContents(DIR_CORE_UPDATES);
        foreach ($updates as $upd) {
            $updFullPath = DIR_CORE_UPDATES . '/' . $upd;
            if (is_dir($updFullPath) && is_writable($updFullPath)) {
                $dispatcher = $updFullPath . '/' . DISPATCHER_FILENAME;
                if (file_exists($dispatcher) && is_writable($dispatcher)) {
                    unlink($dispatcher);
                }
                $index = $updFullPath . '/index.html';
                if (!file_exists($index)) {
                    touch($index);
                }
            }
        }
    }

    public function submit()
    {
        if ($this->validateAction()) {
            try {
                $this->app->make(MutexInterface::class)->execute(Update::MUTEX_KEY, function () {
                    Update::updateToCurrentVersion();
                });
                $this->set('success', t('Upgrade to <b>%s</b> complete!', APP_VERSION));
            } catch (Exception $e) {
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
                $message .= t('Your current website uses a version of concrete5 greater than this one. You cannot upgrade.');
            } else {
                if (version_compare($sav, APP_VERSION, '=')) {
                    $message = t('Your site is already up to date! The current version of concrete5 is <b>%s</b>.', APP_VERSION);
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
