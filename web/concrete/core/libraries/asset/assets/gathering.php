<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Asset_Assets_Gathering extends Asset {
	
	/**
	 * Determines which files in the current file system are part of this asset. Could be one file, could be many.
	 */
	public function getAssetFiles() {
		return array(
			new JavaScriptAssetFile('ccm.gathering.js', false),
			new CSSAssetFile('ccm.gathering.css', false)
		);
	}



}