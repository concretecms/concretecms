<?php
namespace Concrete\Block\CoreGatheringDisplay;
use Loader;
use \Concrete\Core\Block\BlockController;
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
	
	class Controller extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreGatheringDisplay';
		protected $btIsInternal = true;		
		public function getBlockTypeDescription() {
			return t("Proxy block for gathering items added to areas.");
		}
		
		public function getBlockTypeName() {
			return t("Gathering Display");
		}

        public function registerViewAssets()
        {
            $this->requireAsset('core/gathering');
        }

        public function view() {
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
