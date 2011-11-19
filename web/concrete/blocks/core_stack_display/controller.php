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
		
		public function on_page_view() {
			$stack = Stack::getByID($this->stID);
			$p = new Permissions($stack);
			if ($p->canRead()) {
				$blocks = $stack->getBlocks();
				foreach($blocks as $b) {
					$bp = new Permissions($b);
					if ($bp->canRead()) {
						$btc = $b->getInstance();
						if('Controller' != get_class($btc)){
							$btc->outputAutoHeaderItems();
						}
						$btc->runTask('on_page_view', array($view));
					}
				}			
			}
		}

		
		
	}