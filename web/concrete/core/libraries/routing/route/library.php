<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Route extends \Symfony\Component\Routing\Route {

	public function getCallback() {
		$defaults = $this->getDefaults();
		return $defaults['callback'];
	}

	public function getPath() {
		$defaults = $this->getDefaults();
		return $defaults['path'];
	}

}
