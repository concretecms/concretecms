<?
	defined('C5_EXECUTE') or die("Access Denied.");
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
	
	class Concrete5_Controller_Block_CoreStackDisplay extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreStackDisplay';
		protected $btIsInternal = true;		
		public function getBlockTypeDescription() {
			return t("Proxy block for stacks added through the UI.");
		}
		
		public function getBlockTypeName() {
			return t("Stack Display (Core)");
		}
		
		public function getOriginalBlockID() {
			return $this->bOriginalID;
		}

		public function getImportData($blockNode) {
			$args = array();
			$content = (string) $blockNode->stack;
			$stack = Stack::getByName($content);
			$args['stID'] = $stack->getCollectionID();			
			return $args;		
		}
		
		public function export(SimpleXMLElement $blockNode) {			
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
						$csr = $b->getBlockCustomStyleRule();
						if (is_object($csr)) {
							$styleHeader = '#'.$csr->getCustomStyleRuleCSSID(1).' {'. $csr->getCustomStyleRuleText(). "} \r\n";  
							$btc->addHeaderItem("<style type=\"text/css\"> \r\n".$styleHeader.'</style>', 'VIEW');
						}
						$btc->runTask('on_page_view', array($page));
					}
				}			
			}
		}

		
		
	}
