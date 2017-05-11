<?php
namespace Concrete\Controller\Dialog\Language\Update;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Localization\Translation\Local\FactoryInterface as LocalFactory;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteProvider;
use Concrete\Core\Package\BrokenPackage;
use Concrete\Core\Package\PackageService;
use Exception;

class Details extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/language/update/details';

    protected function canAccess()
    {
        return $this->permissions->canViewPage();
    }

    public function view()
    {
        $localeID = (string) $this->request->query->get('locale');
        if ($localeID === '') {
            throw new Exception(t('Missing locale identifier'));
        }
        $package = null;
        if ($this->request->query->has('pkgHandle')) {
            $packageHandle = (string) $this->request->query->get('pkgHandle');
            if ($packageHandle !== '') {
                $packageService = $this->app->make(PackageService::class);
                /* @var PackageService $packageService */
                $package = $packageService->getClass($packageHandle);
                if ($package instanceof BrokenPackage) {
                    throw new Exception(t('Unable to find the specified package'));
                }
            }
        }
        $localFactory = $this->app->make(LocalFactory::class);
        /* @var LocalFactory $localFactory */
        $remoteProvider = $this->app->make(RemoteProvider::class);
        /* @var RemoteProvider $remoteProvider */
        if ($package === null) {
            $coreVersion = $this->app->make('config')->get('concrete.version_installed');
            $this->set('local', $localFactory->getCoreStats($localeID));
            $this->set('remote', $remoteProvider->getCoreStats($coreVersion, $localeID));
        } else {
            $this->set('local', $localFactory->getPackageStats($package, $localeID));
            $this->set('remote', $remoteProvider->getPackageStats($package->getPackageHandle(), $package->getPackageVersion(), $localeID));
        }
        $this->set('dateHelper', $this->app->make('date'));
    }
}
