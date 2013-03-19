<?php
class Concrete5_Model_MarkdownConversationEditor extends Concrete5_Model_PlainTextConversationEditor {
	public function formatConversationMessageBody($cnvMessageBody) {
		loader::library('3rdparty/markdown');

		return Markdown($cnvMessageBody);
	}
}