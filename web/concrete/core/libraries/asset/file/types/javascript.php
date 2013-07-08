<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_JavaScriptAssetFile extends AssetFile {
	
	public function getAssetFileDefaultPosition() {
		return AssetFile::ASSET_FILE_POSITION_FOOTER;
	}

	public function getAssetFileDefaultMinify() {
		return true;
	}

	public function getAssetFileDefaultCombine() {
		return true;
	}

	public function __toString() {
		return '<script type="text/javascript" src="' . $this->file . '"></script>';
	}

}