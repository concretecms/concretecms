<?
namespace Concrete\Core\File\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\File\Version;

class FileVersion extends AbstractEvent {

	protected $fv;

	public function __construct(Version $fv) {
		$this->fv = $fv;
	}

	public function getFileVersionObject() {
		return $this->fv;
	}

}