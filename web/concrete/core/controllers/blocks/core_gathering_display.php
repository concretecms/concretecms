<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Displays an gathering stand-alone in a page.
 *
 * @package Blocks
 * @subpackage Core Gathering Display
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	
	class Concrete5_Controller_Block_CoreGatheringDisplay extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreGatheringDisplay';
		protected $btIsInternal = true;		
		public function getBlockTypeDescription() {
			return t("Proxy block for gathering items added to areas.");
		}
		
		public function getBlockTypeName() {
			return t("Gathering Display (Core)");
		}

		public function view() {
			$req = Request::getInstance();
			$req->requireAsset('core/gathering');
			Loader::helper('overlay')->init(false);
			$gathering = Gathering::getByID($this->gaID);
			if (is_object($gathering)) {
				$list = new GatheringItemList($gathering);
				$list->sortByDateDescending();
				$this->set('gathering', $gathering);
				$this->set('itemList', $list);
			}
		}
		
	}
