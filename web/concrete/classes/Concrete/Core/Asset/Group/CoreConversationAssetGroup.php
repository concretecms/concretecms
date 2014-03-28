<?
namespace Concrete\Core\Asset\Group;
use Concrete\Core\Asset\AssetGroup as AssetGroup;

class CoreConversationAssetGroup extends AssetGroup {	
	public function getAssetPointers() {
		$assetPointers = parent::getAssetPointers();

		$editor = ConversationEditor::getActive();
		foreach((array)$editor->getConversationEditorAssetPointers() as $assetPointer) {
			$assetPointers[] = $assetPointer;
		}
		
		return $assetPointers;
	}

}