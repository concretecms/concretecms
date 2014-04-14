<?php

namespace Concrete\Tests\Core\Foundation;
use Loader;
use \Concrete\Core\Foundation\Service\Locator as ServiceLocator;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class TestClass {}

class TestClass2 {}

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$this->sl = new ServiceLocator();
	}

	public function testRegisterAndRetrieveNewClass() {

		$this->sl->register('testclass', function() {
			return new \Concrete\Tests\Core\Foundation\TestClass();
		});

		$tc = $this->sl->make('testclass');
		$this->assertTrue($tc instanceof \Concrete\Tests\Core\Foundation\TestClass, sprintf('$tc did not match test class. Instead its class was %s', get_class($tc)));

		$tc1 = $this->sl->make('testclass');
		$tc2 = $this->sl->make('testclass');
		$this->assertTrue($tc1 !== $tc2);


	}

	public function testSingletons() {

		$this->sl->singleton('testclass2', function() {
			return new \Concrete\Tests\Core\Foundation\TestClass2();
		});

		$tc1 = $this->sl->make('testclass2');
		$tc2 = $this->sl->make('testclass2');
		$this->assertTrue($tc1 === $tc2);

	}


	public function testInstance() {

		$o = new \stdClass;
		$o->firstname = 'Andrew';
		$o->lastname = 'Embler';
		$this->sl->instance('user', $o);

		$this->_finishInstanceTest();
	}

	protected function _finishInstanceTest() {
		$user = $this->sl->make('user');
		$this->assertTrue($user->firstname == 'Andrew' && $user->lastname == 'Embler');
	}

	public function testClassDefine() {
		$pt = $this->sl->make('\Concrete\Core\Page\Theme\Theme');
		$this->assertTrue($pt instanceof \Concrete\Core\Page\Theme\Theme);


		$class1 = core_class('Core\Page\Theme\Theme');
		$class2 = core_class('Core\Page\Theme\RiverTheme', 'river_theme');
		$class3 = core_class('Core\Page\Theme\Theme', true);

		$this->assertTrue($class1 == '\Concrete\Core\Page\Theme\Theme', 'class1 == ' . $class1);
		$this->assertTrue($class2 == '\Concrete\Package\RiverTheme\Core\Page\Theme\RiverTheme', 'class2 == ' . $class2);
		$this->assertTrue($class3 == '\Application\Core\Page\Theme\Theme', 'class3 == ' . $class3);
	}


	public function testServiceLocatorArrays() {
		$services = array(
			'file' => '\Concrete\Core\File\Service\File',
			'concrete/file' => '\Concrete\Core\File\Service\Application'
		);

		$this->sl->register($services);
		$this->assertTrue($this->sl->isRegistered('file'));

		$filehelper = $this->sl->make('concrete/file');
		$this->assertTrue($filehelper instanceof \Concrete\Core\File\Service\Application);
	}

	public function testServiceGroup() {
		$this->sl->registerGroup('\Concrete\Core\Validation\ValidationServiceGroup');
		$this->sl->registerGroup('\Concrete\Core\Http\HttpServiceGroup');

		$this->assertTrue($this->sl->isRegistered('validation/antispam'));
		$bw1 = $this->sl->make('validation/banned_words');
		$bw2 = $this->sl->make('validation/banned_words');
		$this->assertTrue($bw1 === $bw2);

		// test a non singleton.
		$vt1 = $this->sl->make('validation/token');
		$vt2 = $this->sl->make('validation/token');
		$this->assertFalse($vt1 === $vt2);
		$this->assertTrue($vt1 == $vt2);

		$this->assertTrue($this->sl->make('ajax') instanceof \Concrete\Core\Http\Service\Ajax);
	}

	public function testOverrides() {
		require('fixtures/MyFile.php');
		$services = array(
			'file' => '\Concrete\Core\File\Service\File'
		);

		$this->sl->register($services);

		$filehelper1 = $this->sl->make('file');
		$this->sl->register('file', function() {
			return new \Application\Src\My\File;
		});

		$filehelper2 = $this->sl->make('file');
		$this->assertTrue($filehelper1 instanceof \Concrete\Core\File\Service\File);

	}

	public function testUnregistered() {
		
		require('fixtures/AutonavController.php');

		$controller = $this->sl->make('Concrete\Block\Autonav\Controller');
		$this->assertTrue($controller instanceof \Concrete\Block\Autonav\Controller);

		$mockBlock = new \stdClass;
		$this->sl->register('Concrete\Block\Autonav\Controller', function() use ($mockBlock) {
			return new \Application\Block\Autonav\Controller($mockBlock);
		});

		$controller2 = $this->sl->make('Concrete\Block\Autonav\Controller');
		$this->assertTrue($controller2 instanceof \Concrete\Block\Autonav\Controller);

	}

	public function testAllServiceGroups() {
		$groups = array(
			'\Concrete\Core\File\FileServiceGroup',
			'\Concrete\Core\Encryption\EncryptionServiceGroup',
			'\Concrete\Core\Validation\ValidationServiceGroup',
			'\Concrete\Core\Localization\LocalizationServiceGroup',
			'\Concrete\Core\Feed\FeedServiceGroup',
			'\Concrete\Core\Html\HtmlServiceGroup',
			'\Concrete\Core\Mail\MailServiceGroup',
			'\Concrete\Core\Application\ApplicationServiceGroup',
			'\Concrete\Core\Utility\UtilityServiceGroup',
			'\Concrete\Core\Form\FormServiceGroup',
			'\Concrete\Core\Http\HttpServiceGroup'
		);

		$this->sl->registerGroups($groups);

		$this->assertTrue($this->sl->isRegistered('concrete/ui'));
		$this->assertTrue($this->sl->isRegistered('concrete/ui/help'));
		$this->assertTrue($this->sl->isRegistered('concrete/asset_library'));
		$this->assertTrue($this->sl->isRegistered('mime'));

	}

}