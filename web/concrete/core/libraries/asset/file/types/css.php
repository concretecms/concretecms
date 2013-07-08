<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CSSAssetFile extends AssetFile {
	
	public function getAssetFileDefaultPosition() {
		return AssetFile::ASSET_FILE_POSITION_HEADER;
	}

	public function getAssetFileDefaultMinify() {
		return true;
	}

	public function getAssetFileDefaultCombine() {
		return true;
	}

	public function __toString() {
		return '<link rel="stylesheet" type="text/css" href="' . $this->file . '" />';
	}

}