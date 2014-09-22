<?php
namespace Concrete\Core\View;
use Concrete\Core\Asset\Asset;
use View as ConcreteView;
use \Concrete\Core\Asset\CssAsset;
use \Concrete\Core\Asset\JavascriptAsset;
use User;

class DialogView extends ConcreteView {

	protected function onBeforeGetContents() {
		$this->markHeaderAssetPosition();
	}

	public function outputAssetIntoView($item) {
		$str = '';
		if ($item instanceof CssAsset) {
			$str .= '<script type="text/javascript">';
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "CSS")';
			$str .= '</script>';
		} else if ($item instanceof JavascriptAsset) {
			$str .= '<script type="text/javascript">';
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "JAVASCRIPT")';
			$str .= '</script>';
		} else {
			$str = $item;
		}
		print $str . "\n";
	}

	public function getScopeItems() {
		$items = parent::getScopeItems();
		$u = new User();
		$items['u'] = $u;
		return $items;
	}

    protected function getAssetsToOutput()
    {
        $ouput = parent::getAssetsToOutput();
        $return = array();
        foreach($ouput as $position => $assets) {
            foreach($assets as $asset) {
                if ($asset instanceof Asset) {
                    $asset->setAssetPosition(Asset::ASSET_POSITION_HEADER);
                    $asset->setAssetSupportsMinification(false);
                    $asset->setAssetSupportsCombination(false);
                }
                $return[$position][] = $asset;
            }
        }
        return $return;
    }


    /*
	protected function onAfterGetContents() {
		// now that we have the contents of the tool,
		// we make sure any require assets get moved into the header
		// since that's the only place they work in the AJAX output.
		$r = Request::getInstance();
		$assets = $r->getRequiredAssetsToOutput();
		foreach($assets as $asset) {
			$asset->setAssetPosition(Asset::ASSET_POSITION_HEADER);
            $asset->setAssetSupportsMinification(false);
            $asset->setAssetSupportsCombination(false);
			$this->addOutputAsset($asset);
		}
	}
    */
}
