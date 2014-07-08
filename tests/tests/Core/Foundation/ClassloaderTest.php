<?php

namespace Concrete\Tests\Core\Foundation;
use Loader;
use Core;
use Environment;

class ClassloaderTest extends \PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$this->obj = \Concrete\Core\Foundation\Classloader::getInstance();
	}

	public function testPsr4AutoloadingCore() {
		$this->assertTrue(class_exists('\Concrete\Core\Foundation\Object'));
		$this->assertTrue(class_exists('\Concrete\Core\Application\Application'));
		$this->assertTrue(class_exists('\Concrete\Core\Http\Request'));
	}

	public function testThemeAutoloadingCore() {
		$this->assertTrue(class_exists('\Concrete\Theme\Elemental\PageTheme'));
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
		$request->attributes->set('_controller', '\Concrete\Controller\Install::view');
		$resolver = new \Concrete\Core\Controller\ControllerResolver();
	    $callback = $resolver->getController($request);
		$this->assertTrue($callback[0] instanceof \Concrete\Controller\Install);

		$request = new \Concrete\Core\Http\Request();
		$request->attributes->set('_controller', '\Concrete\Controller\Panel\Page\Design::preview_contents');
		$resolver = new \Concrete\Core\Controller\ControllerResolver();
	    $callback = $resolver->getController($request);
		$this->assertTrue($callback[0] instanceof \Concrete\Controller\Panel\Page\Design);
	}

	public function testRouteControllerOverride() {
		$root = dirname(DIR_BASE_CORE . '../');
		mkdir($root . '/application/controllers/panel/page/', 0777, true);
		copy(dirname(__FILE__) . '/fixtures/design.php', $root . '/application/controllers/panel/page/design.php');

		Core::bind('\Concrete\Controller\Panel\Page\Design', function() {
			return new \Application\Controller\Panel\Page\Design();
		});

		$request = new \Concrete\Core\Http\Request();
		$request->attributes->set('_controller', '\Concrete\Controller\Panel\Page\Design::preview_contents');
		$resolver = new \Concrete\Core\Controller\ControllerResolver();
	    $callback = $resolver->getController($request);

		unlink($root . '/application/controllers/panel/page/design.php');
		rmdir($root . '/application/controllers/panel/page');
		rmdir($root . '/application/controllers/panel');

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
		$env = Environment::get();
		$env->clearOverrideCache();

		$root = dirname(DIR_BASE_CORE . '../');
		mkdir($root . '/application/blocks/core_area_layout/', 0777, true);
		copy(dirname(__FILE__) . '/fixtures/CoreAreaLayoutController.php', $root . '/application/blocks/core_area_layout/controller.php');
		
		$bt = new \BlockType();
		$bt->setBlockTypeHandle('core_area_layout');
		$class = $bt->getBlockTypeClass();
		$classExists = class_exists($class);

		unlink($root . '/application/blocks/core_area_layout/controller.php');
		rmdir($root . '/application/blocks/core_area_layout');

		$this->assertTrue($class == '\Application\Block\CoreAreaLayout\Controller');
		$this->assertTrue($classExists);
	}


	public function testLegacyHelpers() {
		$fh = Loader::helper('file');
		$vh = Loader::helper('validation/error');
		$this->assertTrue($fh instanceof \Concrete\Core\File\Service\File);
		$this->assertTrue($vh instanceof \Concrete\Core\Error\Error);
	}

	

	
}