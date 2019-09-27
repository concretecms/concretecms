<?php

namespace Concrete\Block\DesktopAppStatus;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Updater\Update;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 86400; // check every day

    public function getBlockTypeDescription()
    {
        return t("Displays alerts about your concrete5 site and package updates.");
    }

    public function getBlockTypeName()
    {
        return t("concrete5 Status Messages");
    }

    public function view()
    {
        $config = $this->app->make('config');
        $this->set('current_version', $config->get('concrete.version'));
        $this->set('latest_version', Update::getLatestAvailableVersionNumber());
        $updates = 0;
        $p = new Checker();
        if ($p->canInstallPackages()) {
            $packageService = $this->app->make(PackageService::class);
            $localHandles = [];
            foreach ($packageService->getLocalUpgradeablePackages() as $pkg) {
                ++$updates;
                $localHandles[] = $pkg->getPackageHandle();
            }
            foreach ($packageService->getRemotelyUpgradeablePackages() as $pkg) {
                if (!in_array($pkg->getPackageHandle(), $localHandles)) {
                    ++$updates;
                }
            }
        }
        $this->set('updates', $updates);
    }
}
