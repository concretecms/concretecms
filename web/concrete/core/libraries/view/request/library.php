<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_RequestView extends View {

	/** 
	 * Assets
	 */
	public function addHeaderAsset($item) {
		$this->outputAssets[Asset::ASSET_POSITION_HEADER]['unweighted'][] = $item;
	}
	
	/** 
	 * Function responsible for adding footer items within the context of a view.
	 * @access private
	 */
	public function addFooterAsset($item) {
		$this->outputAssets[Asset::ASSET_POSITION_FOOTER]['unweighted'][] = $item;
	}

	public function addOutputAsset(Asset $asset) {
		if ($asset->getAssetWeight() > 0) {
			$this->outputAssets[$asset->getAssetPosition()]['weighted'][] = $asset;
		} else {
			$this->outputAssets[$asset->getAssetPosition()]['unweighted'][] = $asset;
		}
	}

	/** 
	 * Function responsible for outputting header items
	 * @access private
	 */
	public function markHeaderAssetPosition() {
		print '<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->';
	}
	
	/** 
	 * Function responsible for outputting footer items
	 * @access private
	 */
	public function markFooterAssetPosition() {
		print '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->';
	}

	public function postProcessViewContents($contents) {
		$r = Request::get();
		$assets = $r->getRequiredAssetsToOutput();
		
		foreach($assets as $asset) {
			$this->addOutputAsset($asset);
		}
		
		$contents = $this->replaceAssetPlaceholders($contents);

		// replace any empty placeholders
		$contents = $this->replaceEmptyAssetPlaceholders($contents);

		return $contents;
	}


	protected function sortAssetsByWeightDescending($assetA, $assetB) {
		$weightA = $assetA->getAssetWeight();
		$weightB = $assetB->getAssetWeight();

		if ($weightA == $weightB) {
			return 0;
		}

		return $weightA < $weightB ? 1 : -1;
	}

	protected function sortAssetsByPostProcessDescending($assetA, $assetB) {
		$ppA = ($assetA instanceof Asset && $assetA->assetSupportsPostProcessing());
		$ppB = ($assetB instanceof Asset && $assetB->assetSupportsPostProcessing());
		if ($ppA && $ppB) {
			return 0;
		}
		if ($ppA && !$ppB) {
			return -1;
		}

		if (!$ppA && $ppB) {
			return 1;
		}
		if (!$ppA && !$ppB) {
			return 0;
		}
	}

	protected function postProcessAssets($assets) {
		$c = Page::getCurrentPage();
		if (!is_object($c) || !ENABLE_ASSET_CACHE) {
			return $assets;
		}
		// goes through all assets in this list, creating new URLs and post-processing them where possible.
		$segment = 0;
		$subassets[$segment] = array();
		for ($i = 0; $i < count($assets); $i++) {
			$asset = $assets[$i];
			$nextasset = $assets[$i+1];
			$subassets[$segment][] = $asset;
			if ($asset instanceof Asset && $nextasset instanceof Asset) {
				if ($asset->getAssetType() != $nextasset->getAssetType()) {
					$segment++;
				} else if (!$asset->assetSupportsPostProcessing() || !$nextasset->assetSupportsPostProcessing()) {
					$segment++;
				}
			} else {
				$segment++;
			}
		}

		// now we have a sub assets array with different segments split by post process and non-post-process
		$return = array();
		foreach($subassets as $segment => $assets) {
			if ($assets[0] instanceof Asset && $assets[0]->assetSupportsPostProcessing()) {
				// this entire segment can be post processed together
				$class = Loader::helper('text')->camelcase($assets[0]->getAssetType()) . 'Asset';
				$assets = call_user_func(array($class, 'postprocess'), $assets);
			}
			$return = array_merge($return, $assets);
		}
		return $return;
	}

	protected function replaceEmptyAssetPlaceholders($pageContent) {
		foreach(array('<!--ccm:assets:' . Asset::ASSET_POSITION_HEADER . '//-->', '<!--ccm:assets:' . Asset::ASSET_POSITION_FOOTER . '//-->') as $comment) {
			$pageContent = str_replace($comment, '', $pageContent);
		}
		return $pageContent;
	}

	protected function replaceAssetPlaceholders($pageContent) {
		$outputItems = array();
		foreach($this->outputAssets as $position => $assets) {
			$output = '';
			if (is_array($assets['weighted'])) {
				$weightedAssets = $assets['weighted'];
				usort($weightedAssets, array($this, 'sortAssetsByWeightDescending'));
				$transformed = $this->postProcessAssets($weightedAssets);
				foreach($transformed as $item) {
					$itemstring = (string) $item;
					if (!in_array($itemstring, $outputItems)) {
						$output .= $this->outputAssetIntoView($item);
						$outputItems[] = $itemstring;
					}
				}
			}
			if (is_array($assets['unweighted'])) {
				// now the unweighted
				$unweightedAssets = $assets['unweighted'];
				usort($unweightedAssets, array($this, 'sortAssetsByPostProcessDescending'));
				$transformed = $this->postProcessAssets($unweightedAssets);
				foreach($transformed as $item) {
					$itemstring = (string) $item;
					if (!in_array($itemstring, $outputItems)) {
						$output .= $this->outputAssetIntoView($item);
						$outputItems[] = $itemstring;
					}
				}
			}
			$pageContent = str_replace('<!--ccm:assets:' . $position . '//-->', $output, $pageContent);
		}
		return $pageContent;				
	}
	
	protected function outputAssetIntoView($item) {
		return $item . "\n";			
	}

}