<?php
class ObjectTest extends PHPUnit_Framework_TestCase {
	
	private $obj;
	
	protected function setUp() {
		$this->obj = new Object();
	}

	public function testLoadError() {
		$error = 1234;
		$this->obj->loadError($error);
		$this->assertEquals(1234, $this->obj->error);
	}
	
	public function testIsErrorWithoutArgs() {
		$this->obj->error = 123456;
		$this->assertEquals(123456, $this->obj->isError());
	}
	
	public function testIsErrorWithArgs() {
		$this->obj->error = 1234;
		$this->assertTrue($this->obj->isError(1234));
		$this->assertFalse($this->obj->isError(12345));
	}
	
	public function testGetError() {
		$this->obj->error = 1234;
		$this->assertEquals(1234, $this->obj->getError());
	}
	
		
	public function testSetPropertiesFromArray() {
		$properties = array(
			'test' => 1234,
			'paarden' => 'ponies',
		);
		
		$this->obj->setPropertiesFromArray($properties);
		
		$this->assertEquals(array(
			'error' => null,
			'test' => 1234,
			'paarden' => 'ponies',
		), get_object_vars($this->obj));
	}
	
	public function testCamelcase() {
		$this->assertEquals($this->obj->camelcase('asset_library'), 'AssetLibrary');
		$this->assertEquals($this->obj->camelcase('asset/library'), 'AssetLibrary');
		$this->assertEquals($this->obj->camelcase('asset-library'), 'AssetLibrary');
		$this->assertEquals($this->obj->camelcase('assetLibrary'), 'AssetLibrary');
		$this->assertEquals($this->obj->camelcase('AssetLibrary'), 'AssetLibrary');
	}
}