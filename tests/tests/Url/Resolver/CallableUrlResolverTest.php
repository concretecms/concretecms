<?php

namespace Concrete\Tests\Url\Resolver;

use Concrete\TestHelpers\Url\Resolver\ResolverTestCase;

class CallableUrlResolverTest extends ResolverTestCase
{
    /**
     * @var \Concrete\Core\Url\Resolver\CallableUrlResolver
     */
    protected $urlResolver;

    protected function setUp()
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

    public function testFailSet()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'Resolver not callable');
        $this->urlResolver->setResolver('string');
    }
}
