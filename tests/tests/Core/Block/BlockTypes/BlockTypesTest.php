<?php
class ClassloaderTest extends ConcreteDatabaseTestcase {
	
	protected $fixtures = array();
	protected $tables = array('BlockTypes');

	public function testInstall() {
		$bt1 = BlockType::installBlockType('content');
		BlockType::installBlockType('file');
		$this->assertTrue($bt1->getBlockTypeID() == 1 && $bt1->getBlockTypeHandle() == 'content');
		$bt2 = BlockType::getByID(2);
		$this->assertTrue($bt2->getBlockTypeID() == 2 && $bt2->getBlockTypeHandle() == 'file');
	}



}