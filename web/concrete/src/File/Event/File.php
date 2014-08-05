<?
namespace Concrete\Core\File\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\File\File as ConcreteFile;

class File extends AbstractEvent {

	protected $f;

	public function __construct(ConcreteFile $f) {
		$this->f = $f;
	}

	public function getFileObject() {
		return $this->f;
	}

}