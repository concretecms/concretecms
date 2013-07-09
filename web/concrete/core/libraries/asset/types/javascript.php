<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_JavaScriptAsset extends Asset {
	
	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_FOOTER;
	}

	public function getAssetType() {return 'javascript';}
	
	public function getAssetDefaultMinify() {
		return true;
	}

	public function getAssetPath() {
		return ASSETS_URL_JAVASCRIPT . '/' . $this->getAssetFilename();
	}

	public function getAssetDefaultCombine() {
		return true;
	}

	public function __toString() {
		return '<script type="text/javascript" src="' . $this->getAssetPath() . '"></script>';
	}

	public function __construct($assetHandle) {
		$this->filename = $assetHandle . '.js';
		parent::__construct($assetHandle);
	}

}