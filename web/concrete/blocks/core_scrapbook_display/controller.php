<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	class CoreScrapbookDisplayBlockController extends BlockController {

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