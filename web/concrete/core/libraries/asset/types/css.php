<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CSSAsset extends Asset {
	
	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_HEADER;
	}

	public function getAssetDefaultMinify() {
		return true;
	}

	public function getAssetPath() {
		return ASSETS_URL_CSS . '/' . $this->getAssetFilename();
	}
	
	public function getAssetDefaultCombine() {
		return true;
	}

	public function __toString() {
		return '<link rel="stylesheet" type="text/css" href="' . $this->getAssetPath() . '" />';
	}

	public function __construct($assetHandle) {
		$this->filename = $assetHandle . '.css';
		parent::__construct($assetHandle);
	}
}