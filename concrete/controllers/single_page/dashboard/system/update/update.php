<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Update;

use Concrete\Controller\Upgrade;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Updater\ApplicationUpdate;
use Concrete\Core\Updater\RemoteApplicationUpdate;
use Concrete\Core\Updater\Update as UpdateService;
use Concrete\Core\Updater\UpdateArchive;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Throwable;

class Update extends DashboardPageController
{
    public function on_start()
    {
        parent::on_start();
        $this->app->make(Upgrade::class)->checkSecurity();
    }

    public function view()
    {
        if (!$this->userHasUpgradePermission()) {
            $this->render('/dashboard/system/update/update/no_access');

            return null;
        }
        $upd = $this->app->make(UpdateService::class);
        $updates = $upd->getLocalAvailableUpdates();
        if (count($updates) === 1) {
            $this->setLocalAvailableUpdateView($updates[0]);

            return;
        }
        $this->set('dh', $this->app->make('date'));
        $this->set('currentVersion', $this->app->make('config')->get('concrete.version'));
        $this->set('updates', $updates);
        $remote = $upd->getApplicationUpdateInformation();
        if ($remote instanceof RemoteApplicationUpdate && version_compare($remote->getVersion(), APP_VERSION, '>')) {
            foreach ($updates as $upd) {
                if ($upd->getUpdateVersion() == $remote->getVersion()) {
                    // we have a LOCAL version ready to install that is the same, so we abort
                    $remote = null;
                    break;
                }
            }
        } else {
            $remote = null;
        }
        $this->set('remoteUpdate', $remote);
    }

    public function check_for_updates()
    {
        if (!$this->userHasUpgradePermission()) {
            return $this->buildRedirect($this->action());
        }
        $this->app->make('config')->clear('concrete.misc.latest_version');
        $this->app->make(UpdateService::class)->getLatestAvailableVersionNumber();

        return $this->buildRedirect($this->action());
    }

    public function get_update_diagnostic_information()
    {
        if (!$this->userHasUpgradePermission()) {
            throw new UserMessageException(t('You do not have permission to upgrade this installation of Concrete.'));
        }
        $updateVersion = trim($this->request->request->get('version', ''));
        if ($updateVersion === '') {
            throw new UserMessageException(t('Invalid parameters received.'));
        }
        $upd = ApplicationUpdate::getByVersionNumber($updateVersion);
        if ($upd === null) {
            throw new UserMessageException(t('Unable to find the requested version.'));
        }

        $diagnostic = $upd->getDiagnosticObject();

        return $this->app->make(ResponseFactoryInterface::class)->json($diagnostic->getJSONObject());
    }

    public function download_update()
    {
        if (!$this->userHasUpgradePermission()) {
            return $this->buildRedirect($this->action());
        }

        if (!$this->token->validate('download_update')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!is_dir(DIR_CORE_UPDATES)) {
            $this->error->add(t('The directory %s does not exist.', DIR_CORE_UPDATES));
        } else {
            if (!is_writable(DIR_CORE_UPDATES)) {
                $this->error->add(t('The directory %s must be writable by the web server.', DIR_CORE_UPDATES));
            }
        }

        if (!$this->error->has()) {
            $remote = $this->app->make(UpdateService::class)->getApplicationUpdateInformation();
            if ($remote instanceof RemoteApplicationUpdate) {
                $this->setCanExecuteForever();
                // try to download
                $r = $this->app->make(Marketplace::class)->downloadRemoteFile($remote->getDirectDownloadURL());
                if (is_object($r)) {
                    // error object
                    $this->error->add($r);
                }

                if (!$this->error->has()) {
                    // the file exists in the right spot
                    $ar = new UpdateArchive();
                    try {
                        $ar->install($r);
                    } catch (Throwable $e) {
                        $this->error->add($e->getMessage());
                    }
                }
            } else {
                $this->error->add(t('Unable to retrieve software from update server.'));
            }
        }
        if ($this->error->has()) {
            return $this->view();
        }

        $this->flash('success', t('Version %s has been succesfully downloaded.', $remote->getVersion()));

        return $this->buildRedirect($this->action());
    }

    public function do_update()
    {
        if (!$this->userHasUpgradePermission()) {
            return $this->buildRedirect($this->action());
        }
        $updateVersion = (string) $this->request->request->get('version', '');
        if ($updateVersion === '') {
            $this->error->add(t('Invalid version'));
        } else {
            $upd = ApplicationUpdate::getByVersionNumber($updateVersion);
            if ($upd == null) {
                $this->error->add(t('Invalid version'));
            } else {
                if (version_compare($upd->getUpdateVersion(), APP_VERSION, '<=')) {
                    $this->error->add(
                        t('You may only apply updates with a greater version number than the version you are currently running.')
                    );
                }
            }
        }
        if (!$this->error->has()) {
            $this->setCanExecuteForever();
            $resp = $upd->apply();
            if ($resp === true) {
                return $this->buildRedirect(
                    $this->app->make(ResolverManagerInterface::class)->resolve([
                        '/ccm/system/upgrade/submit?ccm_token=' . $this->token->generate('Concrete\Controller\Upgrade'),
                    ])
                );
            }
            switch ($resp) {
                case ApplicationUpdate::E_UPDATE_WRITE_CONFIG:
                    $this->error->add(
                        t('Unable to write to %1$s. You must make %1$s writable in order to upgrade in this manner.', 'application/config/update.php')
                    );
                    break;
            }
        }

        return $this->view();
    }

    public function start()
    {
        if (!$this->userHasUpgradePermission()) {
            return $this->buildRedirect($this->action());
        }
        $updateVersion = (string) $this->request->request->get('updateVersion', '');
        if ($updateVersion === '') {
            $this->error->add(t('Invalid version'));
        } else {
            $upd = ApplicationUpdate::getByVersionNumber($updateVersion);
            if ($upd == null) {
                $this->error->add(t('Invalid version'));
            } else {
                if (version_compare($upd->getUpdateVersion(), APP_VERSION, '<=')) {
                    $this->error->add(
                        t('You may only apply updates with a greater version number than the version you are currently running.')
                    );
                }
            }
        }

        if (!$this->error->has()) {
            /*
            $resp = $upd->apply();
            if ($resp === true) {
                return $this->buildRedirect(
                    $this->app->make(ResolverManagerInterface::class)->resolve([
                        '/ccm/system/upgrade/submit?ccm_token=' . $this->token->generate('Concrete\Controller\Upgrade'),
                    ])
                );
            }
            switch ($resp) {
                case ApplicationUpdate::E_UPDATE_WRITE_CONFIG:
                    $this->error->add(
                        t('Unable to write to config/site.php. You must make config/site.php writable in order to upgrade in this manner.')
                    );
                    break;
            }
             */
            $this->setLocalAvailableUpdateView($upd);

            return;
        }

        return $this->view();
    }

    protected function userHasUpgradePermission(): bool
    {
        $p = new Checker();

        return (bool) $p->canUpgrade();
    }

    protected function setCanExecuteForever(): bool
    {
        if (ini_get('safe_mode')) {
            return false;
        }
        set_error_handler(function () {}, -1);
        $result = true;
        try {
            if (!@set_time_limit(0)) {
                $result = false;
            }
            if (@ini_set('max_execution_time', 0) === false) {
                $result = false;
            }
        } finally {
            restore_error_handler();
        }

        return $result;
    }

    protected function setLocalAvailableUpdateView(ApplicationUpdate $update): void
    {
        $this->set('ci', $this->app->make('helper/concrete/urls'));
        $this->set('update', $update);
        $this->set('updatePackagesUrl', $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/extend/update']));
        $this->set('installedPackages', $this->app->make(PackageService::class)->getInstalledList());
        $this->render('/dashboard/system/update/update/local_available_update');
    }
}
