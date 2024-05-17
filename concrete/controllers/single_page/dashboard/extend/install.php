<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Marketplace\Connection;
use Concrete\Core\Marketplace\ConnectionInterface;
use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\InvalidDownloadResponseException;
use Concrete\Core\Marketplace\Exception\InvalidPackageException;
use Concrete\Core\Marketplace\Exception\PackageAlreadyExistsException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\Exception\UnableToPlacePackageException;
use Concrete\Core\Marketplace\Model\RemotePackage;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Package\BrokenPackage;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Support\Facade\Package;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Loader;
use Psr\Log\LoggerInterface;
use TaskPermission;

class Install extends DashboardPageController implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /** @var PackageRepositoryInterface */
    protected $repository;
    /** @var Connection|null */
    protected $connection;

    public function on_start()
    {
        parent::on_start();
        @set_time_limit(0);
    }

    public function view(): void
    {
        // Get installed packages
        $packages = $this->app->make(PackageService::class);
        $packages->getRemotelyUpgradeablePackages();

        $packageRepository = $this->getPackageRepository();

        $this->set('packageRepository', $packageRepository);
        $this->set('connection', $this->getConnection());
    }

    protected function getPackageRepository(): PackageRepositoryInterface
    {
        if (!isset($this->repository)) {
            $this->repository = $this->app->make(PackageRepositoryInterface::class);
        }

        return $this->repository;
    }

    protected function getConnection(): ?ConnectionInterface
    {
        if (!isset($this->connection)) {
            $connection = $this->getPackageRepository()->getConnection();
            $this->connection = $connection;
        }

        return $this->connection;
    }

    public function uninstall($pkgID)
    {
        $this->view();
        $tp = new TaskPermission();
        if (!$tp->canUninstallPackages()) {
            return false;
        }

        $pkg = Package::getByID($pkgID);
        if (!is_object($pkg)) {
            $this->redirect('/dashboard/extend/install');
        }
        /** @var Manager $manager */
        $manager = $this->app->make(Manager::class, ['application' => $this->app]);
        $this->set('text', Loader::helper('text'));
        $this->set('pkg', $pkg);
        $this->set('categories', $manager->getPackageItemCategories());
    }

    public function do_uninstall_package()
    {
        $this->view();
        $pkgID = $this->post('pkgID');

        if ($pkgID > 0) {
            $pkg = Package::getByID($pkgID);
        }

        if (!$this->token->validate('uninstall')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $tp = new TaskPermission();
        if (!$tp->canUninstallPackages()) {
            $this->error->add(t('You do not have permission to uninstall packages.'));
        }

        if (!is_object($pkg)) {
            $this->error->add(t('Invalid package.'));
        }

        if (!$this->error->has()) {
            $p = $pkg->getController();
            $test = $p->testForUninstall();

            if (!is_object($test)) {
                $r = Package::uninstall($p);
                if ($this->post('pkgMoveToTrash')) {
                    $r = $pkg->backup();
                    if ($r instanceof ErrorList) {
                        $this->error->add($r);
                    }
                }
                if (!$this->error->has()) {
                    $this->redirect('/dashboard/extend/install', 'package_uninstalled');
                }
            } else {
                $this->error->add($test);
            }
        }

        $this->inspect_package($pkgID);
    }

    public function inspect_package($pkgID = 0)
    {
        if ($pkgID > 0) {
            $pkg = Package::getByID($pkgID);
        }

        if (isset($pkg) && ($pkg instanceof PackageEntity)) {
            /** @var Manager $manager */
            $manager = $this->app->make(Manager::class, ['application' => $this->app]);
            $this->set('categories', $manager->getPackageItemCategories());
            $this->set('pkg', $pkg);
        } else {
            $this->redirect('/dashboard/extend/install');
        }
    }

    public function package_uninstalled()
    {
        $this->view();
        $this->set('message', t('The package has been uninstalled.'));
    }

    public function install_package($package)
    {
        $this->view();
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            $packageService = $this->app->make(PackageService::class);
            $p = $packageService->getClass($package);
            if ($p instanceof BrokenPackage) {
                $this->error->add($p->getInstallErrorMessage());
            } elseif (is_object($p)) {
                $config = $this->app->make('config');
                if ($config->get('concrete.i18n.auto_install_package_languages')) {
                    $connection = $this->getConnection();
                    if ($connection) {
                        $repository = $this->getPackageRepository();
                        $matchingPackages = array_filter(
                            $repository->getPackages($connection),
                            function (RemotePackage $rp) use ($p) {
                                return $rp->handle === $p->getPackageHandle();
                            }
                        );

                        if (count($matchingPackages) > 0) {
                            try {
                                $this->app->make(TranslationsInstaller::class)
                                    ->installMissingPackageTranslations($p);
                            } catch (Exception $x) {
                                $this->getLogger()->error($x);
                            }
                        }
                    }
                }
                if (
                    (!$p->showInstallOptionsScreen()) ||
                    $this->token->validate('install_options_selected')
                ) {
                    $tests = $p->testForInstall();
                    if (is_object($tests)) {
                        $this->error->add($tests);
                    } else {
                        $r = $packageService->install($p, $this->post());
                        if ($r instanceof ErrorList) {
                            $this->error->add($r);
                            if ($p->showInstallOptionsScreen()) {
                                $this->set('showInstallOptionsScreen', true);
                                $this->set('pkg', $p);
                            }
                        } else {
                            $this->redirect('/dashboard/extend/install', 'package_installed', $r->getPackageID());
                        }
                    }
                } else {
                    $this->set('showInstallOptionsScreen', true);
                    $this->set('pkg', $p);
                }
            } else {
                $this->error->add(t('Package controller file not found.'));
            }
        } else {
            $this->error->add(t('You do not have permission to install add-ons.'));
        }
    }

    public function package_installed($pkgID = 0)
    {
        $this->view();
        $pkg = Package::getByID($pkgID);
        $this->set('message', t('Package "%s" version %s has been installed.', t($pkg->getPackageName()), $pkg->getPackageVersion()));
        $this->set('installedPKG', $pkg);
    }

    public function download($remoteId = null)
    {
        $this->view();
        $tp = new TaskPermission();
        if (!$tp->canInstallPackages()) {
            $this->error->add(t('You do not have permission to download add-ons.'));
            return;
        }

        $repository = $this->getPackageRepository();
        $connection = $this->getConnection();
        if (!$connection) {
            $this->error->add(t('This site is not connected to the marketplace.'));
            return;
        }

        $remotePackage = $repository->getPackage($connection, $remoteId);
        if (!$remotePackage instanceof RemotePackage) {
            $this->error->add(t('Invalid marketplace item ID.'));
            return;
        }

        try {
            $this->getLogger()->info('Downloading {name}:{version} from remote package repository.', [
                'name' => $remotePackage->name,
                'handle' => $remotePackage->handle,
                'version' => $remotePackage->version
            ]);
            $repository->download($connection, $remotePackage, true);
        } catch (PackageAlreadyExistsException|UnableToPlacePackageException $e) {
            $this->getLogger()->error('Unable to move {handle}:{version} into package directory.', [
                'name' => $remotePackage->name,
                'handle' => $remotePackage->handle,
                'version' => $remotePackage->version
            ]);
            $this->error->add(t('Unable to move package directory.'));
            return;
        } catch (InvalidPackageException $e) {
            $this->getLogger()->error(
                'Package file for {handle}:{version} did not decompress to an installable package.',
                [
                    'name' => $remotePackage->name,
                    'handle' => $remotePackage->handle,
                    'version' => $remotePackage->version
                ]
            );
            $this->error->add(t('Package file did not decompress to an installable package.'));
            return;
        } catch (InvalidDownloadResponseException $e) {
            $this->getLogger()->error(
                t('Unable to download package {name} for {handle}:{version}. Response message: {message}'),
                [
                    'name' => $remotePackage->name,
                    'handle' => $remotePackage->handle,
                    'version' => $remotePackage->version,
                    'message' => $e->getMessage(),
                ]
            );
            $this->error->add($e->getMessage());
            return;
        }

        $this->set('message', t('Marketplace item %s downloaded successfully.', $remotePackage->name));
    }

    public function delete_package($pkgHandle, $token = null)
    {
        $this->view();
        if ($this->token->validate('delete_package', $token)) {
            $tp = new TaskPermission();
            if ($tp->canUninstallPackages()) {
                $pkg = $this->app->make(PackageService::class)->getClass($pkgHandle);
                if (is_object($pkg)) {
                    if ($pkg->getPackageEntity() && $pkg->getPackageEntity()->isPackageInstalled()) {
                        $this->error->add(t('You can not delete an installed package.'));
                    } else {
                        $r = $pkg->backup();
                        if ($r instanceof ErrorList) {
                            $this->error->add($r);
                        }
                    }
                } else {
                    $this->error->add(t('Invalid package.'));
                }
            } else {
                $this->error->add(t('You do not have permission to uninstall/delete packages.'));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            return new RedirectResponse($this->action('package_deleted'));
        }
    }

    public function package_deleted()
    {
        $this->view();
        $this->set('message', t('The package has been deleted.'));
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_PACKAGES;
    }
}
