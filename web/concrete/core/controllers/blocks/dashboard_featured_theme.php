<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the block that displays featured themes in the dashboard news overlay.
 *
 * @package Blocks
 * @subpackage Dashboard Featured Theme
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_DashboardFeaturedTheme extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btCacheBlockOutputLifetime = 7200;

		protected $btIsInternal = true;		
		protected $btInterfaceWidth = 300;
		protected $btInterfaceHeight = 100;
		
		public function getBlockTypeDescription() {
			return t("Features a theme from concrete5.org.");
		}
		
		public function getBlockTypeName() {
			return t("Dashboard Featured Theme");
		}
		
		public function view() {
			Loader::model('marketplace_remote_item');
			$mri = new MarketplaceRemoteItemList();
			$mri->sortBy('recommended');
			$mri->setItemsPerPage(1);
			$mri->setType('themes');
			$mri->execute();
			$items = $mri->getPage();
			if (is_object($items[0])) {
				$this->set('remoteItem', $items[0]);
			}
		}
		
	}