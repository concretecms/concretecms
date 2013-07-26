<?
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the core scrapbook display block. This block is automatically used when a block is copied into a page from a clipboard. It is a proxy block.
 *
 * @package Blocks
 * @subpackage Core Scrapbook/Clipboard Display
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_CoreScrapbookDisplay extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreScrapbookDisplay';
		protected $btIsInternal = true;		
		public function getBlockTypeDescription() {
			return t("Proxy block for blocks pasted through the scrapbook.");
		}
		
		public function getBlockTypeName() {
			return t("Scrapbook Display (Core)");
		}
		
		public function getOriginalBlockID() {
			return $this->bOriginalID;
		}
		
		public function getSearchableContent() {
			$b = Block::getByID($this->bOriginalID);
			$bc = $b->getInstance();
			if (method_exists($bc, 'getSearchableContent')) {
				return $bc->getSearchableContent();
			}
		}

		public function on_page_view() {
			$b = Block::getByID($this->bOriginalID);
			$bc = $b->getInstance();
			if (method_exists($bc, 'on_page_view')) {
				$bc->on_page_view();
			}
		}

		public function outputAutoHeaderItems() {
			$b = Block::getByID($this->bOriginalID);
			$bvt = new BlockViewTemplate($b);
			$headers = $bvt->getTemplateHeaderItems();
			if (count($headers) > 0) {
				foreach($headers as $h) {
					$this->addHeaderItem($h);
				}
			}
		}
		
		
	}