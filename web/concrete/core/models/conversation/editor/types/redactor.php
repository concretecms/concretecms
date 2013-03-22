<?php
class Concrete5_Model_RedactorConversationEditor extends ConversationEditor {
	public function getConversationEditorHeaderItems() {
		$html = Loader::helper('html');
		return array($html->javascript('redactor.js'),$html->css('redactor.css'));
	}
	public function formatConversationMessageBody($cnvMessageBody) {
		return parent::formatConversationMessageBody($cnvMessageBody);
	}
}