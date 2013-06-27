<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Asset_Assets_Gathering extends Asset {
	
	/**
	 * Determines which files in the current file system are part of this asset. Could be one file, could be many.
	 */
	public function getAssetFiles() {
		return array(
			AssetFile::javascript('ccm.gathering.js')->minify(false),
			AssetFile::css('ccm.gathering.css')->minify(false)
		);
	}



}