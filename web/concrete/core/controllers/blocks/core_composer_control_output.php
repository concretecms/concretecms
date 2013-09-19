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

		public function export(SimpleXMLElement $blockNode) {			
			$outputControl = ComposerOutputControl::getByID($this->cmpOutputControlID);
			if (is_object($outputControl)) {
				$fsc = ComposerFormLayoutSetControl::getByID($outputControl->getComposerFormLayoutSetControlID());
				if (is_object($fsc)) {
					$cnode = $blockNode->addChild('control');
					$cnode->addAttribute('output-control-id', ContentExporter::getComposerOutputControlTemporaryID($fsc));
				}
			}
		}

		public function getImportData($blockNode, $page) {
			$args = array();
			$formLayoutSetControlID = ContentImporter::getComposerFormLayoutSetControlFromTemporaryID((string) $blockNode->control['output-control-id']);
			$formLayoutSetControl = ComposerFormLayoutSetControl::getByID($formLayoutSetControlID);
			$b = $this->getBlockObject();
			$pt = PageTemplate::getByID($page->getPageTemplateID());
			$outputControl = ComposerOutputControl::getByComposerFormLayoutSetControl($pt, $formLayoutSetControl);
			$args['cmpOutputControlID'] = $outputControl->getComposerOutputControlID();			
			return $args;
		}
		
	}



