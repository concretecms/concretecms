<?
namespace Concrete\Core\Conversation\Editor;
use \Concrete\Core\Foundation\Object;
abstract class RedactorConversationEditor extends Object {

	abstract public function getConversationEditorAssetPointers();

	public function setConversationMessageObject(ConversationMessage $message) {
		$this->cnvMessage = $message;
	}

	public function formatConversationMessageBody($cnv,$cnvMessageBody,$config=array()) {
		if (isset($config['htmlawed'])) {
			Loader::library('3rdparty/htmLawed');
			$default = array('safe'=>1,'elements'=>'span, em, b, i, p, strike, font, br, div');
			$conf = array_merge($default,(array)$config['htmlawed']);
			$lawed = htmLawed($cnvMessageBody, $conf);
		} else {
			$lawed = $cnvMessageBody;
		}
		if ($config['mention'] !== false && Config::get('ENABLE_USER_PROFILES')) {
			$users = $cnv->getConversationMessageUsers();
			$needle = array();
			$haystack = array();
			foreach ($users as $user) {
				$needle[] = "@".$user->getUserName();
				$haystack[] = "<a href='".View::url('/account/profile/public', 'view', $user->getUserID())."'>@".$user->getUserName()."</a>";
			}
			return str_ireplace($needle,$haystack,$lawed);
		}
		return $lawed;
	}

	public function getConversationEditorMessageBody() {
		if (!is_object($this->cnvMessage)) {
			return '';
		}
		$cnv = $this->cnvMessage->getConversationObject();
		return $this->formatConversationMessageBody($cnv, $this->cnvMessage->getConversationMessageBody());
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
	public function setConversationObject($cnvObject) {
		$this->cnvObject = $cnvObject;
	}
	public function getConversationObject() {
		return $this->cnvObject;
	}
	public function setConversationEditorInputName($input) {
		$this->cnvEditorInputName = $input;
	}
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