<?php
class Concrete5_Model_MarkdownConversationEditor extends Concrete5_Model_ConversationEditor {

	public function getConversationEditorHeaderItems() {
		return array();
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnvMessageBody) {
		loader::library('3rdparty/markdown');
		return Markdown(parent::formatConversationMessageBody($cnvMessageBody, array('elements'=>'-all')));
	}
}