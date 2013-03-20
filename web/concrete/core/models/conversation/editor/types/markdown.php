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
		$text = Loader::helper('text');
		$md = Markdown(htmlentities($cnvMessageBody));
		return str_replace(array('&amp;lt','&amp;gt'), array('&lt','&gt'), $md);
	}
}