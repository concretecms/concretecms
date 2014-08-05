<?
namespace Concrete\Core\Conversation\Editor;
use Loader;
class PlainTextEditor extends Editor {

	public function getConversationEditorAssetPointers() {
		return array();
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnv,$cnvMessageBody) {
		$text = Loader::helper('text');
		$formatted = nl2br($text->entities($cnvMessageBody));
		return parent::formatConversationMessageBody($cnv,$formatted);
	}
}