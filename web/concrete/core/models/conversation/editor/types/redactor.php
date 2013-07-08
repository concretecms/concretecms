<?php
class Concrete5_Model_RedactorConversationEditor extends ConversationEditor {
	public function getConversationEditorAssets() {
		return array(Asset::getByPath('redactor'));
	}

	public function outputConversationEditorReplyMessageForm() {
		$this->outputConversationEditorAddMessageForm();
	}

	public function formatConversationMessageBody($cnv,$cnvMessageBody) {
		return parent::formatConversationMessageBody($cnv,$cnvMessageBody,array('htmlawed'=>true));
	}
}