<?php
class Concrete5_Model_RedactorConversationEditor extends Concrete5_Model_PlainTextConversationEditor {
	public function getConversationEditorHeaderItems() {
		$html = Loader::helper('html');
		return array($html->javascript('redactor.js'),$html->css('redactor.css'));
	}
	public function formatConversationMessageBody($cnvMessageBody) {
		return $cnvMessageBody;
	}
}