<?php

namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Logging\Logger;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\RemoteItem as MarketplaceRemoteItem;
use Concrete\Core\Package\BrokenPackage;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Package;
use Exception;
use Loader;
use TaskPermission;

class Install extends DashboardPageController
{
    public function on_start()
    {
        parent::on_start();
        @set_time_limit(0);
    }

    public function uninstall($pkgID)
    {
        $tp = new TaskPermission();
        if (!$tp->canUninstallPackages()) {
            return false;
        }

        $pkg = Package::getByID($pkgID);
        if (!is_object($pkg)) {
            $this->redirect('/dashboard/extend/install');
        }
        $manager = new Manager($this->app);
        $this->set('text', Loader::helper('text'));
        $this->set('pkg', $pkg);
        $this->set('categories', $manager->getPackageItemCategories());
    }

    public function do_uninstall_package()
    {
        $pkgID = $this->post('pkgID');

        $valt = Loader::helper('validation/token');

        if ($pkgID > 0) {
            $pkg = Package::getByID($pkgID);
        }

        if (!$valt->validate('uninstall')) {
            $this->error->add($valt->getErrorMessage());
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
                    if (is_object($r)) {
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
            $manager = new Manager($this->app);
            $this->set('categories', $manager->getPackageItemCategories());
            $this->set('pkg', $pkg);
        } else {
            $this->redirect('/dashboard/extend/install');
        }
    }

    public function package_uninstalled()
    {
        $this->set('message', t('The package has been uninstalled.'));
    }

    public function install_package($package)
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            $packageService = $this->app->make(PackageService::class);
            $p = $packageService->getClass($package);
            if ($p instanceof BrokenPackage) {
                $this->error->add($p->getInstallErrorMessage());
            } elseif (is_object($p)) {
                $config = $this->app->make('config');
                if ($config->get('concrete.i18n.auto_install_package_languages')) {
                    $associatedPackages = Marketplace::getAvailableMarketplaceItems(false);
                    if (isset($associatedPackages[$p->getPackageHandle()])) {
                        try {
                            $this->app->make(TranslationsInstaller::class)->installMissingPackageTranslations($p);
                        } catch (Exception $x) {
                            $logger = $this->app->make(Logger::class);
                            $logger->addError($x);
                        }
                    }
                }
                $loader = new ClassLoader();
                $loader->registerPackageCustomAutoloaders($p);
                if (
                    (!$p->showInstallOptionsScreen()) ||
                    Loader::helper('validation/token')->validate('install_options_selected')
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
        $this->set('message', t('The package has been installed.'));
        $this->set('installedPKG', Package::getByID($pkgID));
    }

    public function download($remoteMPID = null)
    {
        $tp = new TaskPermission();
        if ($tp->canInstallPackages()) {
            $mri = MarketplaceRemoteItem::getByID($remoteMPID);

            if (!is_object($mri)) {
                $this->error->add(t('Invalid marketplace item ID.'));

                return;
            }

            $r = $mri->download();
            if ($r != false) {
                $this->error->add($r);
            } else {
                $this->set('message', t('Marketplace item %s downloaded successfully.', $mri->getName()));
            }
        } else {
            $this->error->add(t('You do not have permission to download add-ons.'));
        }
    }
}
