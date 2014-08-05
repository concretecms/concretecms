<?
namespace Concrete\Core\File\Event;
use User;

class FileAccess extends FileVersion {

	protected $u;

	public function setUserObject($u) {
		$this->u = $u;
	}

	public function getUserObject() {
		return $this->u;
	}

}