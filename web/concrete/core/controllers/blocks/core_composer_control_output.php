<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Controller_Block_CoreComposerControlOutput extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreComposerControlOutput';
		protected $btIsInternal = true;		
		public function getBlockTypeDescription() {
			return t("Proxy block for blocks that need to be output through composer.");
		}
		
		public function getBlockTypeName() {
			return t("Composer Control (Core)");
		}
		
		
	}