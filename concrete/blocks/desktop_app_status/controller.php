<?php

namespace Concrete\Block\DesktopAppStatus;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Updater\Update;

class Controller extends BlockController
{
    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = true;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 86400; // check every day

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays alerts about your Concrete site and package updates.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Concrete Status Messages');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('current_version', $config->get('concrete.version'));
        $this->set('latest_version', Update::getLatestAvailableVersionNumber());
        $updates = 0;
        $p = new Checker();
        /** @phpstan-ignore-next-line */
        if ($p->canInstallPackages()) {
            $packageService = $this->app->make(PackageService::class);
            $localHandles = [];
            foreach ($packageService->getLocalUpgradeablePackages() as $pkg) {
                $updates++;
                $localHandles[] = $pkg->getPackageHandle();
            }
            foreach ($packageService->getRemotelyUpgradeablePackages() as $pkg) {
                if (!in_array($pkg->getPackageHandle(), $localHandles, true)) {
                    $updates++;
                }
            }
        }
        $this->set('updates', $updates);
    }
}
