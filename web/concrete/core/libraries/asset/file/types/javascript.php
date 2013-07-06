<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_JavaScriptAssetFile extends AssetFile {
	
	public function getAssetFilePosition() {
		return AssetFile::ASSET_FILE_POSITION_HEADER;
	}

}