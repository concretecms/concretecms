<?
namespace Concrete\Core\Conversation\Editor;
class RedactorConversationEditor extends Editor {

	public function getConversationEditorAssetPointers() {
		$list = AssetList::getInstance();
		$r = $list->getAssetGroup('redactor');
		return $r->getAssetPointers();
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnv,$cnvMessageBody) {
		return parent::formatConversationMessageBody($cnv,$cnvMessageBody,array('htmlawed'=>true));
	}
}