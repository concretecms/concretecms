<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_AssetGroup {
	
	protected $assets = array();

	public function contains(Asset $asset) {
		return in_array($asset, $this->assets);
	}

	public function add(Asset $asset) {
		$this->assets[] = $asset;
	}

	/** 
	 * Loops through all assets, breaks them out into header and footer items
	 */
	public function outputItems() {
		foreach($this->assets as $asset) {
			$files = $asset->getAssetFiles();
			print_r($files);exit;
		}
	}
}