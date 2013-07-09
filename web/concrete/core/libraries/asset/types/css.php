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

	public function getAssetURL() {
		if ($this->local) {
			return ASSETS_URL_CSS . '/' . $this->getAssetFilename();

		} else {
			return $this->getAssetFilename();
		}
	}

	public function getAssetFile() {
		if ($this->local) {
			return ASSETS_URL_CSS . '/' . $this->getAssetFilename();
		} else {
			return $this->getAssetFilename();
		}
	}
	
	public function getAssetDefaultCombine() {
		return true;
	}

	public function __toString() {
		return '<link rel="stylesheet" type="text/css" href="' . $this->getAssetURL() . '" />';
	}

	public function __construct($assetHandle) {
		$this->filename = $assetHandle . '.css';
		parent::__construct($assetHandle);
	}

}