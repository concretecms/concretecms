<?php
namespace Concrete\Block\DesktopAppStatus;

use Package;
use Concrete\Core\Block\BlockController;

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
            $this->set('latest_version', \Concrete\Core\Updater\Update::getLatestAvailableVersionNumber());
            $tp = new \TaskPermission();
            $updates = 0;
            $local = array();
            $remote = array();
            if ($tp->canInstallPackages()) {
                $local = Package::getLocalUpgradeablePackages();
                $remote = Package::getRemotelyUpgradeablePackages();
            }

            // now we strip out any dupes for the total
            $updates = 0;
            $localHandles = array();
            foreach ($local as $_pkg) {
                ++$updates;
                $localHandles[] = $_pkg->getPackageHandle();
            }
            foreach ($remote as $_pkg) {
                if (!in_array($_pkg->getPackageHandle(), $localHandles)) {
                    ++$updates;
                }
            }
            $this->set('updates', $updates);
        }
    }
