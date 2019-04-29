<?php

namespace Concrete\Tests\Url\Resolver;

use Concrete\TestHelpers\Url\Resolver\ResolverTestCase;

class CallableUrlResolverTest extends ResolverTestCase
{
    /**
     * @var \Concrete\Core\Url\Resolver\CallableUrlResolver
     */
    protected $urlResolver;

    public function setUp()
    {
        parent::setUp();
        $this->urlResolver = new \Concrete\Core\Url\Resolver\CallableUrlResolver(function () {});
    }

    public function testCallable()
    {
        $obj = $this;
        $this->urlResolver->setResolver(function () use ($obj) {
            return $obj;
        });

        $this->assertEquals($this, $this->urlResolver->resolve([]));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Resolver not callable
     */
    public function testFailSet()
    {
        $this->urlResolver->setResolver('string');
    }
}
