<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Displays an aggregator item stand-alone in a page.
 *
 * @package Blocks
 * @subpackage Core Aggregator Item
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	
	class Concrete5_Controller_Block_CoreAggregatorItem extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreAggregatorItem';
		protected $btIsInternal = true;		
		public function getBlockTypeDescription() {
			return t("Proxy block for aggregator items added to areas.");
		}
		
		public function getBlockTypeName() {
			return t("Aggregator Item (Core)");
		}

		public function on_page_view() {
			$this->addHeaderItem(Loader::helper('html')->css('ccm.aggregator.css'));
			$this->addFooterItem(Loader::helper('html')->javascript('ccm.aggregator.js'));
		}

		public function view() {
			$item = AggregatorItem::getByID($this->agiID);
			if (is_object($item)) {
				$aggregator = $item->getAggregatorObject();
				$this->set('aggregator', $aggregator);
				$this->set('items', $aggregator->getAggregatorItems());
			}
		}
		
	}