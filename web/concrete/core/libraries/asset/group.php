<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_AssetGroup {
	
	protected $assets = array();

	public function contains($asset) {
		if ($asset instanceof Asset) {
			return in_array($asset, $this->assets);
		}
		if ($asset instanceof AssetGroup) {
			foreach($asset->getAssets() as $groupedAsset) {
				if (in_array($groupedAsset, $this->assets)) {
					return true;
				}
			}
		}

		return false;
	}

	public function add($item) {
		if ($item instanceof Asset) {
			$add = array($item);
		}
		if ($item instanceof AssetGroup) {
			$add = $item->getAssets();
		}
		if (isset($add)) {
			foreach($add as $asset) {
				if (!$this->contains($asset)) {
					$this->assets[] = $asset;
				}
			}
		}
	}

	public function getAssets() {
		return $this->assets;
	}
	
}