<?php
class Concrete5_Model_MarkdownConversationEditor extends ConversationEditor {

	public function getConversationEditorAssetPointers() {
		return array();
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnv,$cnvMessageBody) {
		loader::library('3rdparty/markdown');
		$text = Loader::helper('text');
		$md = Markdown(htmlentities($cnvMessageBody));
		$formatted = str_replace(array('&amp;lt','&amp;gt'), array('&lt','&gt'), $md);
		return parent::formatConversationMessageBody($cnv,$formatted);
	}
}