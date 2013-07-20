<?php
class Concrete5_Model_RedactorConversationEditor extends ConversationEditor {
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