<?
namespace Concrete\Core\File\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\File\File as ConcreteFile;

class FileWithPassword extends File {

	protected $password;

	public function setFilePassword($password) {
		$this->password = $password;
	}

	public function getFilePassword() {
		return $this->password;
	}

}