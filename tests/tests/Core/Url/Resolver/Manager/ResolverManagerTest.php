<?php

class ResolverManagerTest extends PHPUnit_Framework_TestCase
{
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
        $this->assertEquals($this->defaultResponse, $this->manager->resolve(array()));
    }

    public function testPriority()
    {
        $mock = $this->getMock('\Concrete\Core\Url\Resolver\UrlResolverInterface');
        $mock->method('resolve')->willReturn('TEST');

        $this->manager->addResolver('test_resolver', $mock, 12);
        $this->assertEquals('TEST', $this->manager->resolve(array()));
    }

    public function testGetters()
    {
        $mock = $this->getMock('\Concrete\Core\Url\Resolver\UrlResolverInterface');
        $this->manager->addResolver('test_resolver', $mock);

        $this->assertEquals($mock, $this->manager->getResolver('test_resolver'));

        $this->assertArrayHasKey('test_resolver', $this->manager->getResolvers());
        $this->assertEquals($this->defaultResolver, $this->manager->getDefaultResolver());
    }
}
