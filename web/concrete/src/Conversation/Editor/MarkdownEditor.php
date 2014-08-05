<?
namespace Concrete\Core\Conversation\Editor;
use \Michelf\Markdown;
use Loader;
class MarkdownEditor extends Editor {

	public function getConversationEditorAssetPointers() {
		return array();
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnv,$cnvMessageBody) {
		$text = Loader::helper('text');
		$md = Markdown::defaultTransform(htmlentities($cnvMessageBody));
		$formatted = str_replace(array('&amp;lt','&amp;gt'), array('&lt','&gt'), $md);
		return parent::formatConversationMessageBody($cnv,$formatted);
	}
}