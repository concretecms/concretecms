<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Composer;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Marketplace\Exception\UnableToPlacePackageException;
use Concrete\Core\Marketplace\Exception\InvalidDownloadResponseException;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Update extends DashboardPageController
{
    protected function updatePackage(string $pkgHandle): void
    {
        $packageService = $this->app->make(PackageService::class);
        $packageController = $packageService->getClass($pkgHandle);
        $testResult = $packageController->testForUpgrade();
        if ($testResult !== true) {
            $this->error->add($testResult);

            return;
        }
        $previousVersion = $packageController->getPackageEntity()->getPackageVersion();
        $newVersion = $packageController->getPackageVersion();
        if (version_compare($newVersion, $previousVersion, '<=')) {
            $this->error->add(t(
                'Package "%1$s" was not updated because the loaded package controller still reports version %2$s. Clear the PHP opcode cache and try again.',
                t($packageController->getPackageName()) ?: $packageController->getPackageHandle(),
                $newVersion
            ));

            return;
        }

        Localization::getInstance()->withContext(Localization::CONTEXT_SYSTEM, static function () use ($packageController) {
            $packageController->upgradeCoreData();
            $packageController->upgrade();
        });
        $this->set('message',
            t('Package "%1$s" has been updated successfully from version %2$s to version %3$s.',
                t($packageController->getPackageName()) ?: $packageController->getPackageHandle(),
                $previousVersion,
                $newVersion
            )
        );
    }

    public function view()
    {
        $packageRepository = $this->app->make(PackageRepositoryInterface::class);
        $packageService = $this->app->make(PackageService::class);

        $tp = new Checker();
        if ($tp->canInstallPackages()) {
            $skip = $this->app->make(Repository::class)->get('concrete.updates.skip_packages');
            if ($skip !== true) {
                $skipHandles = array_merge(
                    is_array($skip) ? $skip : [],
                    $this->app->make(Composer::class)->getPackagesInstalledViaComposer()
                );
                $packageService->checkPackageUpdates($packageRepository, $skipHandles);
            }
        }

        $connection = $packageRepository->getConnection();
        $localUpdates = $packageService->getLocalUpgradeablePackages();
        $localUpdateHandles = array_map(static function ($package) {
            return $package->getPackageHandle();
        }, $localUpdates);
        $remoteUpdates = array_values(array_filter(
            $packageService->getRemotelyUpgradeablePackages(),
            static function ($package) use ($localUpdateHandles) {
                return !in_array($package->getPackageHandle(), $localUpdateHandles, true);
            }
        ));

        $this->set('connection', $connection);
        $this->set('remotePackages', $connection ? $packageRepository->getPackages($connection, true) : []);
        $this->set('localUpdates', $localUpdates);
        $this->set('remoteUpdates', $remoteUpdates);
    }

    public function do_update($pkgHandle = false)
    {
        if (!$pkgHandle) {
            return $this->view();
        }
        if (!$this->request->isMethod('POST')) {
            $this->error->add(t('Invalid request method.'));

            return $this->view();
        }
        try {
            if (!$this->token->validate('update_addon')) {
                throw new UserMessageException($this->token->getErrorMessage());
            }
            $tp = new Checker();
            if (!$tp->canInstallPackages()) {
                throw new UserMessageException(t('Access Denied.'));
            }
            $this->updatePackage($pkgHandle);
        } catch (UserMessageException $x) {
            $this->error->add($x);
        }
        $this->view();
    }

    public function prepare_remote_upgrade($remoteMPID = 0)
    {
        $packageRepository = $this->app->make(PackageRepositoryInterface::class);
        $packageService = $this->app->make(PackageService::class);
        if (!$this->request->isMethod('POST')) {
            $this->error->add(t('Invalid request method.'));

            return $this->view();
        }

        try {
            if (!$this->token->validate('prepare_remote_upgrade')) {
                throw new UserMessageException($this->token->getErrorMessage());
            }
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
            $this->set('autoUpgradePackageHandle', $mri->handle);
            $this->set('autoUpgradePackageName', t($local->getPackageName()) ?: $local->getPackageHandle());
            $this->set('autoUpgradePackageVersion', $mri->version);
        } catch (UserMessageException|UnableToPlacePackageException|InvalidDownloadResponseException $x) {
            $this->error->add($x);
        }
        $this->view();
    }
}
