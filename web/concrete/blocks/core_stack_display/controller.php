<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class CoreStackDisplayBlockController extends BlockController {

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
			$blockNode->addChild('stack', '<![CDATA[' . $stack->getCollectionName() . ']]>');
			
		}
		
		public function on_page_view() {
			$stack = Stack::getByID($this->stID);
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
						$btc->runTask('on_page_view', array($view));
					}
				}			
			}
		}

		
		
	}