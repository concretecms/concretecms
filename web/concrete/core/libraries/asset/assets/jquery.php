<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Asset_Assets_Jquery extends Asset {
	
	/**
	 * Determines which files in the current file system are part of this asset. Could be one file, could be many.
	 */
	public function getAssetFiles() {
		$f = new JavaScriptAssetFile(ASSETS_URL_JAVASCRIPT . '/jquery.js');
		$f->setAssetFileWeight(100);
		$f->setAssetFilePosition(AssetFile::ASSET_FILE_POSITION_HEADER);
		return array($f);
	}



}