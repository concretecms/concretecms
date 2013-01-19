<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_PageThemeGridFramework {

	public static function getByHandle($ptGridFrameworkHandle) {
		$class = Loader::helper('text')->camelcase($ptGridFrameworkHandle) . 'PageThemeGridFramework';
		$cl = new $class();
		return $cl;
	}

	abstract public function getPageThemeGridFrameworkName();
	abstract public function getPageThemeGridFrameworkRowStartHTML();
	abstract public function getPageThemeGridFrameworkRowEndHTML();
	public function getPageThemeGridFrameworkNumColumns() {
		$classes = $this->getPageThemeGridFrameworkColumnClasses();
		return count($classes);
	}
	
	abstract public function getPageThemeGridFrameworkColumnClasses();


}
