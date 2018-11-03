<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\Importer;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Logging\Logger;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\RemoteItem as MarketplaceRemoteItem;
use Concrete\Core\Package\BrokenPackage;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Support\Facade\Package;
use Concrete\Core\Support\Facade\Url as URL;
use Exception;
use stdClass;

class Install extends DashboardPageController
{
    public $helpers = ['form', 'validation/token', 'concrete/urls', 'concrete/ui'];

    public function on_start()
    {
        parent::on_start();
        @set_time_limit(0);
    }

    public function uninstall($pkgID)
    {
        $tp = new Permissions();
        if (!$tp->canUninstallPackages()) {
            return false;
        }

        $pkg = Package::getByID($pkgID);
        if (!is_object($pkg)) {
            $this->redirect('/dashboard/extend/install');
        }
        $manager = new Manager($this->app);
        $this->set('pkg', $pkg);
        $this->set('categories', $manager->getPackageItemCategories());
    }

    public function do_uninstall_package()
    {
        $pkgID = $this->post('pkgID');
        if ($pkgID > 0) {
            $pkg = Package::getByID($pkgID);
        }

        if (!$this->token->validate('uninstall')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $tp = new Permissions();
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

    /**
     * Install or update a package when dropped in the zone
     *
     * @return \Concrete\Core\Http\ResponseFactoryInterface
     */
    public function drop_package()
    {
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        try {
            if (!$this->token->validate('drop_package')) {
                throw new Exception($this->token->getErrorMessage());
            }

            // Check if there is a file
            if (!$this->request->files->has('file')) {
                throw new Exception(t('You must upload a file.'));
            }

            // Retrieve the uploaded file and check its validity
            $uploadedFile = $this->request->files->get('file');
            if (!$uploadedFile instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                throw new Exception(Importer::getErrorMessage(Importer::E_FILE_INVALID));
            }
            if (!$uploadedFile->isValid()) {
                throw new Exception(Importer::getErrorMessage($uploadedFile->getError()));
            }

            // Get the file name
            $name = $uploadedFile->getClientOriginalName();
            if (empty($name)) {
                throw new Exception(Importer::getErrorMessage(Importer::E_FILE_INVALID));
            }

            // Move the file to the packages directory
            $file = $uploadedFile->move(DIR_PACKAGES, $name);

            // Unzip the package
            $zip = $this->app->make('helper/zip');
            $zip->unzip(DIR_PACKAGES.'/'.$name, DIR_PACKAGES);

            // Get the package name
            $packageName = pathinfo($name, PATHINFO_FILENAME);

            // If the package is already installed, let's try to update it
            $packageService = $this->app->make(PackageService::class);
            $package = $packageService->getClass($packageName);
            $updatePackage = false;
            if (is_object($package)) {
                $entity = Package::getByHandle($package->getPackageHandle());
                if (is_object($entity) && version_compare($package->getPackageVersion(), $entity->getPackageVersion(), '>')) {

                    // Use the update page controller to update the package
                    $updatePackageController = $this->app->make(\Concrete\Controller\SinglePage\Dashboard\Extend\Update::class);
                    $updatePackageController->do_update($packageName, $this->error);
                    $updatePackage = true;
                }
            }

            // Install the package
            if (!$updatePackage) {
                $package = $this->install_package($packageName, true);
                if (is_object($package) && $package->showInstallOptionsScreen()) {
                    throw new Exception(t('This package can not be installed automatically because it has an options page, close this dialog window to continue the installation manually.'));
                }
            }
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }

        // Delete the archive
        try {
            if (file_exists(DIR_PACKAGES.'/'.$name)) {
                unlink(DIR_PACKAGES.'/'.$name);
            }
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }

        // Create response object
        $response = new stdClass();

        // Return the error if there is one
        if ($this->error->has()) {
            $response->error = true;
            $response->message = t($this->error->toText());

            // Reload the page if the package has an options page
            if (is_object($package) && $package->showInstallOptionsScreen()) {
                $response->targetPage = (string) URL::to('/dashboard/extend/install');
            }
        }

        // Return package informations
        else {
            if ($updatePackage) {
                $response->message = t('The package has been updated successfully.');
                $response->targetPage = (string) URL::to('/dashboard/extend/install');
            } else {
                $response->message = t('The package has been installed.');
                $response->targetPage = (string) URL::to('/dashboard/extend/install/package_installed');
            }
            $response->pkID = $package->getPackageID();
            $response->pkHandle = $package->getPackageHandle();
        }

        return $responseFactory->json($response);
    }

    /**
     * Display the options page to configure a package
     *
     * @param int $pkID id of the package
     */
    public function configure_package($pkID)
    {
        $packageService = $this->app->make(PackageService::class);
        $package = $packageService->getByID($pkID);
        if (is_object($package)) {
            $this->set('showInstallOptionsScreen', true);
            $this->set('pkg', $package->getController());
        }
    }

    public function install_package($package, $fromDropzone = false)
    {
        $tp = new Permissions();
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
                if (!$fromDropzone && (!$p->showInstallOptionsScreen() || $this->token->validate('install_options_selected'))) {
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
                    if ($fromDropzone) {
                        // Do not install the package if it has a options screen
                        if ($p->showInstallOptionsScreen()) {
                            return $p;
                        }

                        // Test and install the package
                        else {
                            $tests = $p->testForInstall();
                            if (is_object($tests)) {
                                $this->error->add($tests);
                            } else {
                                $r = $packageService->install($p, $this->post());
                                if ($r instanceof ErrorList) {
                                    $this->error->add($r);
                                } else {
                                    return $r;
                                }
                            }
                        }
                    }
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
        $tp = new Permissions();
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
