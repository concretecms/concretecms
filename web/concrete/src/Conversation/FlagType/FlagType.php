<?php
namespace Concrete\Core\Conversation\FlagType;
use Loader;
use \Concrete\Core\Foundation\Object;
class FlagType extends Object {

	public $id;
	public $handle;

	public function getConversationFlagTypeHandle() { return $this->handle; }
	public function getConversationFlagTypeID() { return $this->id; }

	public function __construct($id=false,$handle=false) {
		$this->init($id,$handle);
	}

	public function init($id,$handle) {
		if ($this->id && $this->handle) {
			throw new \Exception(t('Flag type already initialized.'));
		}
		$this->id     = $id;
		$this->handle = $handle;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('DELETE FROM ConversationFlaggedMessageTypes WHERE cnvMessageFlagTypeID=?',array($this->id));
	}

	public static function getByID($id) {
		$db = Loader::db();
		$handle = $db->getOne("SELECT cnvMessageFlagTypeHandle FROM ConversationFlaggedMessageTypes WHERE cnvMessageFlagTypeID=?",array($id));
		if (!$handle) return false;
		$bw = new static($id, $handle);
		return $bw;
	}

	public static function getByhandle($handle) {
		$db = Loader::db();
		$handle = strtolower($handle);
		$id = $db->getOne("SELECT cnvMessageFlagTypeID FROM ConversationFlaggedMessageTypes WHERE cnvMessageFlagTypeHandle=?",array($handle));
		if (!$id) return false;
		$bw = new static($id, $handle);
		return $bw;
	}

	public static function add($handle) {
		if (!$handle) return false;
		$db = Loader::db();
		$handle = strtolower($handle);
		if ($ft = static::getByhandle($handle)) return $ft;
		$db->execute('INSERT INTO ConversationFlaggedMessageTypes (cnvMessageFlagTypeHandle) VALUES (?)',array($handle));
		$id = $db->Insert_ID();
		return new static($id, $handle);
	}

}
