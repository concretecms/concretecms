<?php
abstract class Concrete5_Model_ConversationEditor extends Object {

	abstract public function getConversationEditorHeaderItems();
	public function formatConversationMessageBody($cnvMessageBody,$config=array()) {
		Loader::library('3rdparty/htmLawed');
		$default = array('safe'=>1,'elements'=>'span, em, b, i, p, strike, font, br, div');
		$config = array_merge($default,(array)$config);
		return htmLawed($cnvMessageBody, $config);
	}

	public function outputConversationEditorAddMessageForm() {
		$env = Environment::get();
		$editor = $this;
		$path = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_CONVERSATIONS . '/' . DIRNAME_CONVERSATION_EDITOR . '/' . $this->cnvEditorHandle . '/' . FILENAME_CONVERSATION_EDITOR_FORM_MESSAGE, $this->getPackageHandle());
		include($path);		
	}

	public function outputConversationEditorReplyMessageForm() {
		$env = Environment::get();
		$editor = $this;
		$path = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_CONVERSATIONS . '/' . DIRNAME_CONVERSATION_EDITOR . '/' . $this->cnvEditorHandle . '/' . FILENAME_CONVERSATION_EDITOR_FORM_REPLY, $this->getPackageHandle());
		include($path);		
	}

	protected $cnvEditorInputName = 'cnvMessageBody';

	public function getConversationEditorInputName() {return $this->cnvEditorInputName;}
	public function getConversationEditorHandle() { return $this->cnvEditorHandle;}
	public function getConversationEditorName() { return $this->cnvEditorName;}
	public function isConversationEditorActive() { return $this->cnvEditorIsActive;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	public function getPackageObject() {return Package::getByID($this->pkgID);}
	
	public static function getActive() {
		$db = Loader::db();
		$cnvEditorHandle = $db->GetOne('select cnvEditorHandle from ConversationEditors where cnvEditorIsActive = 1');
		if ($cnvEditorHandle) { 
			return ConversationEditor::getByHandle($cnvEditorHandle);
		}
	}
	
	public static function getByHandle($cnvEditorHandle) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvEditorHandle, cnvEditorIsActive, pkgID, cnvEditorName from ConversationEditors where cnvEditorHandle = ?', array($cnvEditorHandle));
		if (is_array($r) && $r['cnvEditorHandle']) {
			$class = Loader::helper('text')->camelcase($r['cnvEditorHandle']) . 'ConversationEditor';
			$sc = new $class();
			$sc->setPropertiesFromArray($r);
			return $sc;
		}
	}
	
	public static function add($cnvEditorHandle, $cnvEditorName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into ConversationEditors (cnvEditorHandle, cnvEditorName, pkgID) values (?, ?, ?)', array($cnvEditorHandle, $cnvEditorName, $pkgID));
		return ConversationEditor::getByHandle($cnvEditorHandle);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ConversationEditors where cnvEditorHandle = ?', array($this->cnvEditorHandle));
	}
	
	public function activate() {
		$db = Loader::db();
		ConversationEditor::deactivateAll();
		$db->Execute('update ConversationEditors set cnvEditorIsActive = 1 where cnvEditorHandle = ?', array($this->cnvEditorHandle));
	}
	
	public static function deactivateAll() {
		$db = Loader::db();
		$db->Execute('update ConversationEditors set cnvEditorIsActive = 0');
	}
		
	public static function getList() {
		$db = Loader::db();
		$cnvEditorHandles = $db->GetCol('select cnvEditorHandle from ConversationEditors order by cnvEditorHandle asc');
		$editors = array();
		foreach($cnvEditorHandles as $cnvEditorHandle) {
			$cnvEditor = ConversationEditor::getByHandle($cnvEditorHandle);
			$editors[] = $cnvEditor;
		}
		return $editors;
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$cnvEditorHandles = $db->GetCol('select cnvEditorHandle from ConversationEditors where pkgID = ? order by cnvEditorHandle asc', array($pkg->getPackageID()));
		$editors = array();
		foreach($cnvEditorHandles as $cnvEditorHandle) {
			$cnvEditor = ConversationEditor::getByHandle($cnvEditorHandle);
			$editors[] = $cnvEditor;
		}
		return $editors;
	}
	
	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('conversationeditors');
		
		foreach($list as $sc) {
			$activated = 0;
			$type = $nxml->addChild('editor');
			$type->addAttribute('handle', $sc->getConversationEditorHandle());
			$type->addAttribute('name', $sc->getConversationEditorName());
			$type->addAttribute('package', $sc->getPackageHandle());
			$type->addAttribute('activated', $sc->isConversationEditorActive());
		}
	}
		
	public function hasOptionsForm() {
		$env = Environment::get();
		$rec = $env->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_CONVERSATIONS . '/' . DIRNAME_CONVERSATION_EDITOR . '/' . $this->cnvEditorHandle . '/' . FILENAME_CONVERSATION_EDITOR_OPTIONS, $this->getPackageHandle());
		return $rec->exists();
	}	
	
}