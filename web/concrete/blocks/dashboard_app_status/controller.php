<?php
namespace Concrete\Block\DashboardAppStatus;
use Loader;
use Package;
use \Concrete\Core\Block\BlockController;
/**
 * The controller for the Dashboard App Status block. It is added to the dashboard news page/overlay and handles display of available updates.
 *
 * @package Blocks
 * @subpackage Dashboard App Status
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */	
	class Controller extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btCacheBlockOutputLifetime = 86400; // check every day

		protected $btIsInternal = true;		
		
		public function getBlockTypeDescription() {
			return t("Displays update and welcome back information on your dashboard.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard App Status");
		}
		
		public function view() {
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
			foreach($local as $_pkg) {
				$updates++;
				$localHandles[] = $_pkg->getPackageHandle();
			}
			foreach($remote as $_pkg) {
				if (!in_array($_pkg->getPackageHandle(), $localHandles)) {
					$updates++;
				}
			}
			$this->set('updates', $updates);
		}
		
	}