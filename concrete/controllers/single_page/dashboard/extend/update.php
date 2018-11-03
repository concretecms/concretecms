<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\RemoteItem as MarketplaceRemoteItem;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Support\Facade\Package;
use Exception;

class Update extends DashboardPageController
{
    public function do_update($pkgHandle = false, $errorList = null)
    {
        $tp = new Permissions();
        if ($tp->canInstallPackages()) {
            if ($pkgHandle) {
                $packageService = $this->app->make(PackageService::class);
                $pkg = $packageService->getClass($pkgHandle);
                $r = $pkg->testForUpgrade();
                if ($r !== true) {
                    if (is_object($errorList)) {
                        $errorList->add($r);
                    } else {
                        $this->error->add($r);
                    }
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
                        if (is_object($errorList)) {
                            $errorList->add($e);
                        } else {
                            $this->error->add($e);
                        }
                    }
                }
            }
        }
        $this->view();
    }

    public function view()
    {
        $tp = new Permissions();
        if ($tp->canInstallPackages()) {
            $mi = Marketplace::getInstance();
            if ($mi->isConnected()) {
                Marketplace::checkPackageUpdates();
            }
        }
    }

    public function prepare_remote_upgrade($remoteMPID = 0)
    {
        $tp = new Permissions();
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
