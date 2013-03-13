<?

class Concrete5_Model_PlainTextConversationEditor extends ConversationEditor {

	public function getConversationEditorHeaderItems() {
		return array();
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnvMessageBody) {
		$text = Loader::helper('text');
		return nl2br($text->entities($cnvMessageBody));
	}
}