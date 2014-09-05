<?php
namespace Concrete\Core\Page\Theme\GridFramework;
use Loader;
use Core;
abstract class GridFramework {

	public static function getByHandle($pThemeGridFrameworkHandle) {
		$class = '\\Concrete\\Core\\Page\\Theme\\GridFramework\\Type\\' . camelcase($pThemeGridFrameworkHandle);
		$cl = Core::make($class);
		return $cl;
	}

	abstract public function getPageThemeGridFrameworkName();
	abstract public function getPageThemeGridFrameworkRowStartHTML();
	abstract public function getPageThemeGridFrameworkRowEndHTML();
    abstract public function getPageThemeGridFrameworkContainerStartHTML();
    abstract public function getPageThemeGridFrameworkContainerEndHTML();

    public function getPageThemeGridFrameworkNumColumns() {
		$classes = $this->getPageThemeGridFrameworkColumnClasses();
		return count($classes);
	}

	public function hasPageThemeGridFrameworkOffsetClasses() {
		$classes = $this->getPageThemeGridFrameworkColumnClasses();
		return count($classes) > 0;
	}

	abstract public function getPageThemeGridFrameworkColumnClasses();
	abstract public function getPageThemeGridFrameworkColumnOffsetClasses();

	public function getPageThemeGridFrameworkColumnClassForSpan($span) {
		$span = $span - 1;
		$classes = $this->getPageThemeGridFrameworkColumnClasses();
		return $classes[$span];
	}

	public function getPageThemeGridFrameworkColumnClassForOffset($offset) {
		$offset = $offset - 1;
		$classes = $this->getPageThemeGridFrameworkColumnOffsetClasses();
		return $classes[$offset];
	}

}
