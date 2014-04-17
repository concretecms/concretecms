<?
namespace Concrete\Core\Page;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use User;

class Event extends AbstractEvent {

	protected $page;
	protected $user;

	public function __construct(Page $c) {
		$this->page = $c;
	}

	public function setUser(User $u) {
		$this->user = $u;
	}

	public function getPageObject() {
		return $this->page;
	}

	public function getUserObject() {
		return $this->user;
	}
}