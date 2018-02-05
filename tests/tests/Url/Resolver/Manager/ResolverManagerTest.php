<?php

namespace Concrete\Tests\Url\Resolver\Manager;

use Concrete\TestHelpers\CreateClassMockTrait;
use PHPUnit_Framework_TestCase;

class ResolverManagerTest extends PHPUnit_Framework_TestCase
{
    use CreateClassMockTrait;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManager
     */
    protected $manager;

    protected $defaultResolver;

    protected $defaultResponse = 'DEFAULT';

    protected function setUp()
    {
        $obj = $this;

        $this->defaultResolver = new \Concrete\Core\Url\Resolver\CallableUrlResolver(
            function ($resolver, $arguments, $resolved) use ($obj) {
                if ($resolved) {
                    return $resolved;
                }

                return $obj->getDefaultResponse();
            });

        $this->manager = new \Concrete\Core\Url\Resolver\Manager\ResolverManager('default', $this->defaultResolver);
    }

    /**
     * @return string
     */
    public function getDefaultResponse()
    {
        return $this->defaultResponse;
    }

    public function testDefaultResolve()
    {
        $this->assertEquals($this->defaultResponse, $this->manager->resolve([]));
    }

    public function testPriority()
    {
        $mock = $this->createMockFromClass('\Concrete\Core\Url\Resolver\UrlResolverInterface');
        $mock->method('resolve')->willReturn('TEST');

        $this->manager->addResolver('test_resolver', $mock, 12);
        $this->assertEquals('TEST', $this->manager->resolve([]));
    }

    public function testGetters()
    {
        $mock = $this->createMockFromClass('\Concrete\Core\Url\Resolver\UrlResolverInterface');
        $this->manager->addResolver('test_resolver', $mock);

        $this->assertEquals($mock, $this->manager->getResolver('test_resolver'));

        $this->assertArrayHasKey('test_resolver', $this->manager->getResolvers());
        $this->assertEquals($this->defaultResolver, $this->manager->getDefaultResolver());
    }
}
