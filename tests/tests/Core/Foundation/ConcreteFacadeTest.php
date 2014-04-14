<?php

namespace Concrete\Tests\Core\Foundation;
use Concrete;

class ConcreteFacadeTest extends \PHPUnit_Framework_TestCase {
	
	public function testFacade() {
		Concrete::bind('file', '\Concrete\Core\File\Service\File');

		$fh = Concrete::make('file');
		$this->assertTrue($fh instanceof \Concrete\Core\File\Service\File);
	}
}