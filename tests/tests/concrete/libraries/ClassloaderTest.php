<?php
class ClassloaderTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$this->obj = \Concrete\Core\Foundation\Classloader::getInstance();
	}

	public function testPsr0AutoloadingCore() {
		$this->assertTrue(class_exists('Concrete\Core\Foundation\Object'));
		$this->assertTrue(class_exists('Concrete\Core\Dispatcher'));
		$this->assertTrue(class_exists('Concrete\Core\Http\Request'));
	}

	public function testThemeAutoloadingCore() {
		$this->assertTrue(class_exists('Concrete\Theme\Concrete'));
	}

	public function testOverrideableCoreClassesCore() {
		$c = new Cache();
		$this->assertTrue($c instanceof \Concrete\Core\Foundation\Cache\Cache);
	}

	public function testRouteController() {
		$request = new \Concrete\Core\Http\Request();
		$request->attributes->set('_controller', 'Controller\Install::view');
		$resolver = new \Concrete\Core\Controller\ControllerResolver();
	    $callback = $resolver->getController($request);
		$this->assertTrue($callback[0] instanceof \Concrete\Controller\Install);

		$request = new \Concrete\Core\Http\Request();
		$request->attributes->set('_controller', 'Controller\Panel\Page\Design::preview_contents');
		$resolver = new \Concrete\Core\Controller\ControllerResolver();
	    $callback = $resolver->getController($request);
		$this->assertTrue($callback[0] instanceof \Concrete\Controller\Panel\Page\Design);
	}

	public function testRouteControllerOverride() {
		$root = dirname(DIR_BASE_CORE . '../');
		mkdir($root . '/controllers/Panel/Page/', 0777, true);
		copy(dirname(__FILE__) . '/fixtures/Design.php', $root . '/controllers/Panel/Page/Design.php');

		$request = new \Concrete\Core\Http\Request();
		$request->attributes->set('_controller', 'Controller\Panel\Page\Design::preview_contents');
		$resolver = new \Concrete\Core\Controller\ControllerResolver();
	    $callback = $resolver->getController($request);

		unlink($root . '/controllers/Panel/Page/Design.php');
		rmdir($root . '/controllers/Panel/Page');
		rmdir($root . '/controllers/Panel');

		$this->assertTrue($callback[0] instanceof \Application\Controller\Panel\Page\Design);
		$this->assertTrue($callback[0] instanceof \Concrete\Controller\Panel\Page\Design);

	}

	public function testHelpers() {
		$fh = Loader::helper('file');
		$vh = Loader::helper('validation/error');
		$this->assertTrue($fh instanceof Concrete\Helper\File);
		$this->assertTrue($vh instanceof Concrete\Helper\Validation\Error);
	}

}