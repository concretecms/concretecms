<?php
namespace Concrete\Tests\Core\Controller;

class ControllerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testNamespaceCollision()
    {
        class_exists('Concrete\Core\Controller\ControllerResolver');
        class_exists('Concrete\Core\Controller\ApplicationAwareControllerResolver');

        $this->assertTrue(true);
    }
}
