<?php

namespace Concrete\Tests\Foundation;

use Core;
use PHPUnit_Framework_TestCase;

class ConcreteFacadeTest extends PHPUnit_Framework_TestCase
{
    public function testFacade()
    {
        Core::bind('file', '\Concrete\Core\File\Service\File');

        $fh = Core::make('file');
        $this->assertTrue($fh instanceof \Concrete\Core\File\Service\File);
    }
}
