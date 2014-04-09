<?php

namespace Concrete\Tests\Core\Foundation;
use Loader;

class ClassloaderTest extends \PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$this->obj = \Concrete\Core\Foundation\Classloader::getInstance();
	}

	public function testPsr4AutoloadingCore() {
		$this->assertTrue(class_exists('\Concrete\Core\Foundation\Object'));
		$this->assertTrue(class_exists('\Concrete\Core\Application\Dispatcher'));
		$this->assertTrue(class_exists('\Concrete\Core\Http\Request'));
	}

	public function testThemeAutoloadingCore() {
		$this->assertTrue(class_exists('\Concrete\Theme\TwitterBootstrap\PageTheme'));
		$this->assertTrue(class_exists('\Concrete\Theme\Concrete\PageTheme'));
	}

	public function testJobAutoloadingCore() {
		$this->assertTrue(class_exists('\Concrete\Job\IndexSearchAll'));
		$this->assertTrue(class_exists('\Concrete\Job\IndexSearch'));
		$this->assertTrue(class_exists('\Concrete\Job\GenerateSitemap'));
		$this->assertTrue(class_exists('\Concrete\Job\UpdateGatherings'));
		$this->assertTrue(class_exists('\Concrete\Job\IndexSearchAll'));
		$this->assertTrue(class_exists('\Concrete\Job\RemoveOldPageVersions'));
		$this->assertTrue(class_exists('\Concrete\Job\ProcessEmail'));
	}

	public function testOverrideableCoreClassesCore() {
		$c = new \Cache();
		$this->assertTrue($c instanceof \Concrete\Core\Cache\Cache);
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

	public function testAttributes() {
		$at = new \Concrete\Core\Attribute\Type();
		$at->atHandle = 'boolean';
		$at->loadController();
		$this->assertTrue(class_exists('\Concrete\Attribute\Boolean\Controller'));
	}

	public function testBlocks() {
		$bt = new \BlockType();
		$bt->setBlockTypeHandle('core_stack_display');
		$class = $bt->getBlockTypeClass();
		$classExists = class_exists($class);
		$this->assertTrue($class == '\Concrete\Block\CoreStackDisplay\Controller');
		$this->assertTrue($classExists);

	}

	public function testBlockControllerOverride() {
		$root = dirname(DIR_BASE_CORE . '../');
		mkdir($root . '/blocks/CoreAreaLayout/', 0777, true);
		copy(dirname(__FILE__) . '/fixtures/CoreAreaLayoutController.php', $root . '/blocks/CoreAreaLayout/Controller.php');

		$bt = new \BlockType();
		$bt->setBlockTypeHandle('core_area_layout');
		$class = $bt->getBlockTypeClass();
		$classExists = class_exists($class);

		unlink($root . '/blocks/CoreAreaLayout/controller.php');
		rmdir($root . '/blocks/CoreAreaLayout');

		$this->assertTrue($class == '\Application\Block\CoreAreaLayout\Controller');
		$this->assertTrue($classExists);
	}

	public function testHelpers() {
		$fh = helper('file');
		$vh = Loader::helper('validation/error');
		$this->assertTrue($fh instanceof \Concrete\Helper\File);
		$this->assertTrue($vh instanceof \Concrete\Helper\Validation\Error);
	}

	public function testHelperOverrides() {
		$root = dirname(DIR_BASE_CORE . '../');
		mkdir($root . '/helpers/validation/', 0777, true);
		copy(dirname(__FILE__) . '/fixtures/captcha.php', $root . '/helpers/validation/captcha.php');

		$fh = Loader::helper('validation/captcha');

		unlink($root . '/helpers/validation/captcha.php');
		rmdir($root . '/helpers/validation');

		$this->assertTrue($fh instanceof \Application\Helper\Validation\Captcha);
		$this->assertTrue($fh instanceof \Concrete\Helper\Validation\Captcha);
	}

}