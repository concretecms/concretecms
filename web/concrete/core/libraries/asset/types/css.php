<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CSSAsset extends Asset {
	
	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_HEADER;
	}

	public function getAssetType() {return 'css';}

	public function getAssetDefaultMinify() {
		return true;
	}

	public function populateAssetURLFromFilename($filename) {
		if ($this->local) {
			$this->assetURL = ASSETS_URL_CSS . '/' . $filename;
		} else {
			$this->assetURL = $filename;
		}
	}

	public function populateAssetPathFromFilename($filename) {
		if ($this->local) {
			$this->assetPath = DIR_BASE_CORE . '/' . $filename;
		}
	}

	public function getAssetDefaultCombine() {
		return true;
	}

	public function __toString() {
		return '<link rel="stylesheet" type="text/css" href="' . $this->getAssetURL() . '" />';
	}

}