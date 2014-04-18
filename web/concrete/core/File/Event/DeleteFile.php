<?
namespace Concrete\Core\File\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;

class DeleteFile extends File {

	protected $proceed = true;

	public function cancelDelete() {
		$this->proceed = false;
	}

	public function proceed() {
		return $this->proceed;
	}

}