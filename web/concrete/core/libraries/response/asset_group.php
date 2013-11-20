<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_ResponseAssetGroup {

	static $group = null;
	protected $providedAssetGroupUnmatched = array();
	protected $outputAssets = array();
	
	public static function get() {
		if (null === self::$group) {
			self::$group = new ResponseAssetGroup();
		}
		return self::$group;
	}

	public function __construct() {
		$this->requiredAssetGroup = new AssetGroup();
		$this->providedAssetGroup = new AssetGroup();
	}

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

	public function getAssetsToOutput() {
		$assets = $this->getRequiredAssetsToOutput();
		foreach($assets as $asset) {
			$this->addOutputAsset($asset);
		}
		return $this->outputAssets;
	}

	/** 
	 * Notes in the current request that a particular asset has already been provided.
	 */
	public function markAssetAsIncluded($assetType, $assetHandle = false) {
		$list = AssetList::getInstance();
		if ($assetType && $assetHandle) {
			$asset = $list->getAsset($assetType, $assetHandle);
		} else {
			$assetGroup = $list->getAssetGroup($assetType);
		}

		if ($assetGroup) {
			$this->providedAssetGroup->addGroup($assetGroup);
		} else if ($asset) {
			$ap = new AssetPointer($asset->getAssetType(), $asset->getAssetHandle());
			$this->providedAssetGroup->add($ap);
		} else {
			$ap = new AssetPointer($assetType, $assetHandle);
			$this->providedAssetGroupUnmatched[] = $ap;
		}
	}

	/** 
	 * Adds a required asset to this request. This asset will attempt to be output or included
	 * when a view is rendered
	 */
	public function requireAsset($assetType, $assetHandle = false) {
		$list = AssetList::getInstance();
		if ($assetType instanceof Asset) {
			$this->requiredAssetGroup->addAsset($assetType);
		} else if ($assetType && $assetHandle) {
			$ap = new AssetPointer($assetType, $assetHandle);
			$this->requiredAssetGroup->add($ap);
		} else {
			$r = $list->getAssetGroup($assetType);
			$this->requiredAssetGroup->addGroup($r);
		}
	}

	/** 
	 * Returns all required assets
	 */
	public function getRequiredAssets() {
		return $this->requiredAssetGroup;
	}

	protected function filterProvidedAssets($asset) {
		foreach($this->providedAssetGroup->getAssetPointers() as $pass) {
			if ($pass->getHandle() == $asset->getHandle() && $pass->getType() == $asset->getType()) {
				return false;
			}
		}

		// now is the unmatched assets something that matches this asset?
		// (ie, is it a path-style match, like bootstrap/* )
		foreach($this->providedAssetGroupUnmatched as $assetPointer) {
			if ($assetPointer->getType() == $asset->getType() && fnmatch($assetPointer->getHandle(), $asset->getHandle())) {
				return false;
			}
		}

		return true;

	}

	/** 
	 * Returns only assets that are required but that aren't also in the providedAssetGroup
	 */
	public function getRequiredAssetsToOutput() {
		$required = $this->requiredAssetGroup->getAssetPointers();
		$assetPointers = array_filter($required, array('ResponseAssetGroup', 'filterProvidedAssets'));
		$assets = array();
		$al = AssetList::getInstance();
		foreach($assetPointers as $ap) {
			$asset = $ap->getAsset();
			if ($asset instanceof Asset) {
				$assets[] = $asset;
			}
		}
		// also include any hard-passed $assets into the group. This is rare but it's used for handle-less
		// assets like layout css
		$assets = array_merge($this->requiredAssetGroup->getAssets(), $assets);
		return $assets;
	}
	
}