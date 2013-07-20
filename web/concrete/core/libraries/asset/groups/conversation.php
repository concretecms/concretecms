<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CoreConversationAssetGroup extends AssetGroup {
	
	public function getAssetPointers() {
		$assetPointers = parent::getAssetPointers();

		$editor = ConversationEditor::getActive();
		foreach((array)$editor->getConversationEditorAssetPointers() as $assetPointer) {
			$assetPointers[] = $assetPointer;
		}
		
		return $assetPointers;
	}

}