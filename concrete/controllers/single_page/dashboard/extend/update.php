<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Page\Controller\DashboardPageController;
use TaskPermission;
use Package;
use Marketplace;
use Concrete\Core\Marketplace\RemoteItem as MarketplaceRemoteItem;
use Localization;
use Loader;
use Exception;

class Update extends DashboardPageController
{
    public function on_start()
    {
        $this->error = Loader::helper('validation/error');
    }
    public function do_update($pkgHandle = false)
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            if ($pkgHandle) {
                $pkg = \Concrete\Core\Support\Facade\Package::getClass($pkgHandle);
                $r = $pkg->testForUpgrade();
                if ($r !== true) {
                    $this->error->add($r);
                } else {
                    $p = Package::getByHandle($pkgHandle);
                    $loc = Localization::getInstance();
                    $loc->pushActiveContext(Localization::CONTEXT_SYSTEM);
                    try {
                        $p->upgradeCoreData();
                        $p->upgrade();
                        $loc->popActiveContext();
                        $this->set('message', t('The package has been updated successfully.'));
                    } catch (Exception $e) {
                        $loc->popActiveContext();
                        $this->error->add($e);
                    }
                }
            }
        }
        $this->view();
    }

    public function view()
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            $mi = Marketplace::getInstance();
            if ($mi->isConnected()) {
                Marketplace::checkPackageUpdates();
            }
        }
    }

    public function prepare_remote_upgrade($remoteMPID = 0)
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            $mri = MarketplaceRemoteItem::getByID($remoteMPID);

            if (!is_object($mri)) {
                $this->set('error', array(t('Invalid marketplace item ID.')));

                return;
            }

            $local = Package::getbyHandle($mri->getHandle());
            if (!is_object($local) || $local->isPackageInstalled() == false) {
                $this->error->add(t('Package Not Found.'));
                return;
            }

            $r = $mri->downloadUpdate();

            if ($r != false) {
                $this->error->add($r);
            } else {
                $this->redirect('/dashboard/extend/update', 'do_update', $mri->getHandle());
            }
        }
    }
}
