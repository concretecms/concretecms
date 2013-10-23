<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_DialogRequestView extends RequestView {
	
	protected function onBeforeGetContents() {
		$this->markHeaderAssetPosition();
	}

	public function outputAssetIntoView($item) {
		$str = '';
		if ($item instanceof CssAsset) {
			$str .= $item;
		} else {
			$str .= '<script type="text/javascript">';	
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "JAVASCRIPT")';
			$str .= '</script>';
		}
		print $str . "\n";
	}

	protected function onAfterGetContents() {
		// now that we have the contents of the tool,
		// we make sure any require assets get moved into the header
		// since that's the only place they work in the AJAX output.
		$r = Request::get();
		$assets = $r->getRequiredAssetsToOutput();
		foreach($assets as $asset) {
			$asset->setAssetPosition(Asset::ASSET_POSITION_HEADER);
			$this->addOutputAsset($asset);
		}
	}
}
