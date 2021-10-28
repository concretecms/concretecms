<?php

namespace Concrete\Tests\Foundation;

use Core;
use Concrete\Tests\TestCase;

class ConcreteFacadeTest extends TestCase
{
    public function testFacade()
    {
        Core::bind('file', '\Concrete\Core\File\Service\File');

        $fh = Core::make('file');
        $this->assertTrue($fh instanceof \Concrete\Core\File\Service\File);
    }
}
