<?
namespace Concrete\Core\Routing;
class Route extends \Symfony\Component\Routing\Route {

	public function getCallback() {
		$defaults = $this->getDefaults();
		return $defaults['callback'];
	}

	public function getPath() {
		$defaults = $this->getDefaults();
		return $defaults['path'];
	}

}
