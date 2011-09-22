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
		
	}