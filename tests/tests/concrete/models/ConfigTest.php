<?php
class ConfigTest extends PHPUnit_Framework_TestCase {

	private function getStoreMock() {
		return $this->getMock(
			'ConfigStore',
			array('get', 'load', 'getListByPackage', 'delete', 'set'),
			array(),
			'',
			false //disable construct
		);
	}
	
	public function testGetWithoutPackageAndFull() {
		$configvalue = new ConfigValue();
		$configvalue->key = 'something';
		$configvalue->timestamp = 12345;
		$configvalue->value = 'paarden';
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('get')->
			with($this->equalTo('something'))->
			will($this->returnValue($configvalue));
		
		Config::setStore($store);
		
		$this->assertEquals(Config::get('something'), 'paarden');
	}
	
	public function testGetWithoutPackageAndWithFull() {
		$configvalue = new ConfigValue();
		$configvalue->key = 'something';
		$configvalue->timestamp = 12345;
		$configvalue->value = 'paarden';
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('get')->
			with($this->equalTo('something'))->
			will($this->returnValue($configvalue));
		
		Config::setStore($store);
		
		$this->assertEquals(Config::get('something', true), $configvalue);
	}
	
	public function testGetWithPackageAndWithoutFull() {
		$configvalue = new ConfigValue();
		$configvalue->key = 'something';
		$configvalue->timestamp = 12345;
		$configvalue->value = 'paarden';
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('get')->
			with($this->equalTo('something'), $this->equalTo(112))->
			will($this->returnValue($configvalue));
		
		Config::setStore($store);
		
		$packagemock = $this->getMock('stdClass', array('getPackageID'));
		$packagemock->expects($this->once())->
			method('getPackageID')->
			will($this->returnValue(112));
			
		$config = new Config();
		$config->setPackageObject($packagemock);
		
		$this->assertEquals($config->get('something'), 'paarden');
	}
	
	public function testGetWithPackageAndWithFull() {
		$configvalue = new ConfigValue();
		$configvalue->key = 'something';
		$configvalue->timestamp = 12345;
		$configvalue->value = 'paarden';
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('get')->
			with($this->equalTo('something'), $this->equalTo(112))->
			will($this->returnValue($configvalue));
		
		Config::setStore($store);
		
		$packagemock = $this->getMock('stdClass', array('getPackageID'));
		$packagemock->expects($this->once())->
			method('getPackageID')->
			will($this->returnValue(112));
			
		$config = new Config();
		$config->setPackageObject($packagemock);
		
		$this->assertEquals($config->get('something', true), $configvalue);
	}
	
	public function testGetListByPackage() {
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('getListByPackage')->
			with($this->equalTo(112))->
			will($this->returnValue(array('PAARDEN', 'PONNIES')));
		
		$packagemock = $this->getMock('stdClass', array('getPackageID', 'config'));
		$packagemock->expects($this->once())->
			method('getPackageID')->
			will($this->returnValue(112));
			
		$packagemock->expects($this->any())->
			method('config')->
			will($this->onConsecutiveCalls('PAARDEN', 'PONNIES'));
		
		Config::setStore($store);
		
		$ret = Config::getListByPackage($packagemock);
		$this->assertEquals($ret, array('PAARDEN', 'PONNIES'));
	}
	
	public function testGetAndDefineFound()
	{
		$configvalue = new ConfigValue();
		$configvalue->key = 'testGetAndDefineFound';
		$configvalue->timestamp = 12345;
		$configvalue->value = 'paarden';
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('get')->
			with($this->equalTo('testGetAndDefineFound'))->
			will($this->returnValue($configvalue));
		
		Config::setStore($store);
		Config::getAndDefine('testGetAndDefineFound', 'honden');
		$this->assertTrue(defined('testGetAndDefineFound'));
		$this->assertEquals(testGetAndDefineFound, 'paarden');
	}
	
	public function testGetAndDefineNotFound()
	{
		$configvalue = null;//new ConfigValue();
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('get')->
			with($this->equalTo('testGetAndDefineNotFound'))->
			will($this->returnValue($configvalue));
		
		Config::setStore($store);

		Config::getAndDefine('testGetAndDefineNotFound', 'honden');

		$this->assertTrue(defined('testGetAndDefineNotFound'));
		$this->assertEquals(testGetAndDefineNotFound, 'honden');
	}
	
	public function testClear()
	{
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('delete')->
			with($this->equalTo('test'), $this->equalTo(null));
		
		Config::setStore($store);
		Config::clear('test');
	}
	
	public function testClearWithPackage()
	{
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('delete')->
			with($this->equalTo('test'), $this->equalTo(112));
		
		Config::setStore($store);
		
		$packagemock = $this->getMock('stdClass', array('getPackageID'));
		$packagemock->expects($this->once())->
			method('getPackageID')->
			will($this->returnValue(112));
			
		$config = new Config();
		$config->setPackageObject($packagemock);
		$config->clear('test', 112);
	}
	
	public function testSave()
	{
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('set')->
			with($this->equalTo('test'), $this->equalTo('someValue'), $this->equalTo(null));
		
		Config::setStore($store);
		Config::save('test', 'someValue');
	}
	
	public function testSaveWithPackage()
	{
		$store = $this->getStoreMock();
		$store->expects($this->once())->
			method('set')->
			with($this->equalTo('test'), $this->equalTo('someValue'), $this->equalTo('112'));
		
		Config::setStore($store);
		$packagemock = $this->getMock('stdClass', array('getPackageID'));
		$packagemock->expects($this->once())->
			method('getPackageID')->
			will($this->returnValue('112'));
			
		$config = new Config();
		$config->setPackageObject($packagemock);
		$config->save('test', 'someValue');
	}
}