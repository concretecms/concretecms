<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CSSAssetFile extends AssetFile {
	
	public function getAssetFilePosition() {
		return AssetFile::ASSET_FILE_POSITION_HEADER;
	}

	

}