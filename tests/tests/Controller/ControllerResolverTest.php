<?php

namespace Concrete\Tests\Controller;

use Concrete\Tests\TestCase;

class ControllerResolverTest extends TestCase
{
    public function testNamespaceCollision()
    {
        class_exists('Concrete\Core\Controller\ControllerResolver');
        class_exists('Concrete\Core\Controller\ApplicationAwareControllerResolver');

        $this->assertTrue(true);
    }
}
