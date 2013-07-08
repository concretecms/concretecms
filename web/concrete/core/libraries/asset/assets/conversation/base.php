<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Asset_Assets_Conversation_Base extends Asset {
	
	/**
	 * Determines which files in the current file system are part of this asset. Could be one file, could be many.
	 */
	public function getAssetFiles() {
		$files = array(
			new JavaScriptAssetFile(ASSETS_URL_JAVASCRIPT . '/ccm.conversations.js'),
			new JavaScriptAssetFile(ASSETS_URL_JAVASCRIPT . '/dropzone.js'),
			new CSSAssetFile(ASSETS_URL_CSS . '/ccm.conversations.css')
		);

		$editor = ConversationEditor::getActive();
		foreach((array)$editor->getConversationEditorAssets() as $asset) {
			foreach($asset->getAssetFiles() as $file) {
				$files[] = $file;
			}
		}

		return $files;
	}



}