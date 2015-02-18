<?php
namespace Concrete\Block\CoreStackDisplay;
use Stack;
use Permissions;
use Loader;
/**
 * The controller for the stack display block. This is an internal proxy block that is inserted when a stack's contents are displayed in a page.
 *
 * @package Blocks
 * @subpackage Core Stack Display
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

	protected $btCacheBlockRecord = true;
	protected $btTable = 'btCoreStackDisplay';
	protected $btIsInternal = true;		
	public function getBlockTypeDescription() {
		return t("Proxy block for stacks added through the UI.");
	}
	
	public function getBlockTypeName() {
		return t("Stack Display");
	}
	
	public function getOriginalBlockID() {
		return $this->bOriginalID;
	}

	public function getImportData($blockNode, $page) {
		$args = array();
		$content = (string) $blockNode->stack;
		$stack = Stack::getByName($content);
		$args['stID'] = $stack->getCollectionID();			
		return $args;		
	}
	
	public function export(\SimpleXMLElement $blockNode) {			
		$stack = Stack::getByID($this->stID);
		if (is_object($stack)) {
			$cnode = $blockNode->addChild('stack');
			$node = dom_import_simplexml($cnode);
			$no = $node->ownerDocument;
			$node->appendChild($no->createCDataSection($stack->getCollectionName()));
		}
	}
	
	public function on_page_view($page) {
		$stack = Stack::getByID($this->stID);
		if (!is_object($stack)) {
			return false;
		}
		$p = new Permissions($stack);
		if ($p->canViewPage()) {
			$blocks = $stack->getBlocks();
			foreach($blocks as $b) {
				$bp = new Permissions($b);
				if ($bp->canViewBlock()) {
					$btc = $b->getInstance();
					if('Controller' != get_class($btc)){
						$btc->outputAutoHeaderItems();
					}
					$csr = $b->getCustomStyle();
					if (is_object($csr)) {
                        $styleHeader = '<style type="text/css" data-style-set="' . $csr->getStyleSet()->getID() . '">' . $csr->getCSS() . '</style>';
						$btc->addHeaderItem($styleHeader);
					}
					$btc->runTask('on_page_view', array($page));
				}
			}			
		}
	}

	
	
}
