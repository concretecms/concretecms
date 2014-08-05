<?
namespace Concrete\Core\File\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\File\Set\Set;

class FileSet extends AbstractEvent {

	protected $fs;

	public function __construct(Set $fs) {
		$this->fs = $fs;
	}

	public function getFileSetObject() {
		return $this->fs;
	}

}