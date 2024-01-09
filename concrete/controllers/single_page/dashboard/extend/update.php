<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Marketplace\RemoteItem as MarketplaceRemoteItem;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Update extends DashboardPageController
{
    public function view()
    {
        $packageRepository = $this->app->make(PackageRepositoryInterface::class);
        $packageService = $this->app->make(PackageService::class);

        $tp = new Checker();
        if ($tp->canInstallPackages()) {
            $skip = $this->app->make(Repository::class)->get('concrete.updates.skip_packages');

            if ($skip !== true) {
                $packageService->checkPackageUpdates($packageRepository, (array) $skip);
            }
        }

        $connection = $packageRepository->getConnection();
        $this->set('connection', $connection);
        $this->set('remotePackages', $connection ? $packageRepository->getPackages($connection, true) : []);
        $this->set('localUpdates', $packageService->getLocalUpgradeablePackages());
        $this->set('remoteUpdates', $packageService->getRemotelyUpgradeablePackages());
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
            $this->set('message',
                t('Package "%1$s" has been updated successfully from version %2$s to version %3$s.',
                    t($packageController->getPackageName()) ?: $packageController->getPackageHandle(),
                    $previousVersion,
                    $packageController->getPackageVersion()
                )
            );
        } catch (UserMessageException $x) {
            $this->error->add($x);
        }
        $this->view();
    }

    public function prepare_remote_upgrade($remoteMPID = 0)
    {
        $packageRepository = $this->app->make(PackageRepositoryInterface::class);
        $packageService = $this->app->make(PackageService::class);

        try {
            $tp = new Checker();
            if (!$tp->canInstallPackages()) {
                throw new UserMessageException(t('Access Denied.'));
            }

            $connection = $packageRepository->getConnection();
            if (!$connection) {
                throw new UserMessageException(t('Site not connected to marketplace.'));
            }

            $mri = $packageRepository->getPackage($connection, $remoteMPID);
            if (!$mri) {
                throw new UserMessageException(t('Invalid marketplace item ID.'));
            }
            $local = $packageService->getByHandle($mri->handle);
            if ($local === null || !$local->isPackageInstalled()) {
                throw new UserMessageException(t('Package Not Found.'));
            }

            $packageRepository->download($connection, $mri, true);
            return $this->buildRedirect(['/dashboard/extend/update', 'do_update', $mri->handle]);
        } catch (UserMessageException $x) {
            $this->error->add($x);
        }
        $this->view();
    }
}
