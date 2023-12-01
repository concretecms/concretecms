<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\RemoteItem as MarketplaceRemoteItem;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Update extends DashboardPageController
{
    public function view()
    {
        $tp = new Checker();
        if ($tp->canInstallPackages()) {
            $mi = Marketplace::getInstance();
            if ($mi->isConnected()) {
                Marketplace::checkPackageUpdates();
            }
        }
    }

    public function do_update($pkgHandle = false)
    {
        if (!$pkgHandle) {
            return $this->view();
        }
        try {
            $tp = new Checker();
            if (!$tp->canInstallPackages()) {
                throw new UserMessageException(t('Access Denied.'));
            }
            $packageService = $this->app->make(PackageService::class);
            $packageController = $packageService->getClass($pkgHandle);
            $testResult = $packageController->testForUpgrade();
            if ($testResult !== true) {
                $this->error->add($testResult);

                return $this->view();
            }
            $previousVersion = $packageController->getPackageEntity()->getPackageVersion();
            Localization::getInstance()->withContext(Localization::CONTEXT_SYSTEM, static function () use ($packageController) {
                $packageController->upgradeCoreData();
                $packageController->upgrade();
            });
            $this->set('message', t('Package "%1$s" has been updated successfully from version %2$s to version %3$s.', t($packageController->getPackageName()), $previousVersion, $packageController->getPackageVersion()));
        } catch (UserMessageException $x) {
            $this->error->add($x);
        }
        $this->view();
    }

    public function prepare_remote_upgrade($remoteMPID = 0)
    {
        try {
            $tp = new Checker();
            if (!$tp->canInstallPackages()) {
                throw new UserMessageException(t('Access Denied.'));
            }
            $mri = MarketplaceRemoteItem::getByID($remoteMPID);
            if (!is_object($mri)) {
                throw new UserMessageException(t('Invalid marketplace item ID.'));
            }
            $local = $this->app->make(PackageService::class)->getByHandle($mri->getHandle());
            if ($local === null || !$local->isPackageInstalled()) {
                throw new UserMessageException(t('Package Not Found.'));
            }
            $error = $mri->downloadUpdate();
            if (!$error) {
                return $this->buildRedirect(['/dashboard/extend/update', 'do_update', $mri->getHandle()]);
            }
            $this->error->add($error);
        } catch (UserMessageException $x) {
            $this->error->add($x);
        }
        $this->view();
    }
}
