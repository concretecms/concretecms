<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Backup;

use Concrete\Controller\Upgrade;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Updater\ApplicationUpdate;
use Concrete\Core\Updater\Archive;
use Config;
use Exception;
use Loader;

class UpdateArchive extends Archive
{

    public function __construct()
    {
        parent::__construct();
        $this->targetDirectory = DIR_CORE_UPDATES;
    }

    public function install($file)
    {
        parent::install($file, true);
    }

}

if (!ini_get('safe_mode')) {
    @set_time_limit(0);
    ini_set('max_execution_time', 0);
}

class Update extends DashboardPageController
{

    public function check_for_updates()
    {
        Config::clear('concrete.misc.latest_version');
        \Concrete\Core\Updater\Update::getLatestAvailableVersionNumber();
        $this->redirect('/dashboard/system/backup/update');
    }

    public function on_start()
    {
        parent::on_start();
        $this->error = Loader::helper('validation/error');
        id(new Upgrade())->checkSecurity();
    }

    public function download_update()
    {
        $p = new \Permissions();
        if (!$p->canUpgrade()) {
            return false;
        }

        $vt = Loader::helper('validation/token');
        if (!$vt->validate('download_update')) {
            $this->error->add($vt->getErrorMessage());
        }
        if (!is_dir(DIR_CORE_UPDATES)) {
            $this->error->add(t('The directory %s does not exist.', DIR_CORE_UPDATES));
        } else {
            if (!is_writable(DIR_CORE_UPDATES)) {
                $this->error->add(t('The directory %s must be writable by the web server.', DIR_CORE_UPDATES));
            }
        }

        if (!$this->error->has()) {
            $remote = \Concrete\Core\Updater\Update::getApplicationUpdateInformation();
            if (is_object($remote)) {
                // try to download
                $r = \Marketplace::downloadRemoteFile($remote->url);
                if (empty($r) || $r == \Package::E_PACKAGE_DOWNLOAD) {
                    $response = array(\Package::E_PACKAGE_DOWNLOAD);
                } else {
                    if ($r == \Package::E_PACKAGE_SAVE) {
                        $response = array($r);
                    }
                }

                if (isset($response)) {
                    $errors = \Package::mapError($response);
                    foreach ($errors as $e) {
                        $this->error->add($e);
                    }
                }

                if (!$this->error->has()) {
                    // the file exists in the right spot
                    $ar = new UpdateArchive();
                    try {
                        $ar->install($r);
                    } catch (Exception $e) {
                        $this->error->add($e->getMessage());
                    }

                }
            } else {
                $this->error->add(t('Unable to retrieve software from update server.'));
            }
        }
        $this->view();
    }

    function view()
    {
        $p = new \Permissions();
        if ($p->canUpgrade()) {

            $upd = new \Concrete\Core\Updater\Update();
            $updates = $upd->getLocalAvailableUpdates();
            $remote = $upd->getApplicationUpdateInformation();
            $this->set('updates', $updates);
            if (is_object($remote) && version_compare($remote->version, APP_VERSION, '>')) {
                // loop through local updates
                $downloadableUpgradeAvailable = true;
                foreach ($updates as $upd) {
                    if ($upd->getUpdateVersion() == $remote->version) {
                        // we have a LOCAL version ready to install that is the same, so we abort
                        $downloadableUpgradeAvailable = false;
                        $this->set('showDownloadBox', false);
                        break;
                    }
                }

                $this->set('downloadableUpgradeAvailable', $downloadableUpgradeAvailable);
                $this->set('update', $remote);
            } else {
                $this->set('downloadableUpgradeAvailable', false);
            }
            $this->set('canUpgrade', true);
        }
    }

    public function do_update()
    {

        $p = new \Permissions();
        if (!$p->canUpgrade()) {
            return false;
        }
        $updateVersion = $this->post('updateVersion');
        if (!$updateVersion) {
            $this->error->add(t('Invalid version'));
        } else {
            $upd = ApplicationUpdate::getByVersionNumber($updateVersion);
        }

        if (!is_object($upd)) {
            $this->error->add(t('Invalid version'));
        } else {
            if (version_compare($upd->getUpdateVersion(), APP_VERSION, '<=')) {
                $this->error->add(
                    t(
                        'You may only apply updates with a greater version number than the version you are currently running.'));
            }
        }

        if (!$this->error->has()) {
            $resp = $upd->apply();
            if ($resp !== true) {
                switch ($resp) {
                    case ApplicationUpdate::E_UPDATE_WRITE_CONFIG:
                        $this->error->add(
                            t(
                                'Unable to write to config/site.php. You must make config/site.php writable in order to upgrade in this manner.'));
                        break;
                }
            } else {
                $token = Loader::helper("validation/token");
                \Redirect::to('/ccm/system/upgrade/submit?ccm_token=' . $token->generate('Concrete\Controller\Upgrade'))->send();
                exit;
            }
        }
        $this->view();

    }
}
