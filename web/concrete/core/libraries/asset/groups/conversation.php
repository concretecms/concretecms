<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CoreConversationAssetGroup extends AssetGroup {
	
	public function getAssets() {
		$assets = parent::getAssets();

		$editor = ConversationEditor::getActive();
		foreach((array)$editor->getConversationEditorAssets() as $asset) {
			$assets[] = $asset;
		}

		return $assets;
	}

}