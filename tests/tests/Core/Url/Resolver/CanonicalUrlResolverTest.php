<?php

require_once __DIR__ . "/ResolverTestCase.php";

class CanonicalUrlResolverTest extends ResolverTestCase
{

    protected function setUp()
    {
        $this->urlResolver = new \Concrete\Core\Url\Resolver\CanonicalUrlResolver();
    }

    public function testConfig()
    {
        $canonical = "http://example.com:1337";
        $old_value = \Config::get('concrete.seo.canonical_url');
        \Config::set('concrete.seo.canonical_url', $canonical);

        $this->assertEquals(
            (string) \Concrete\Core\Url\Url::createFromUrl($canonical)->setPath(\Core::getApplicationRelativePath()),
            (string) $this->urlResolver->resolve(array()));

        \Config::set('concrete.seo.canonical_url', $old_value);
    }

    public function testFromRequest()
    {
        $mock = $this->getMock('\Concrete\Core\Http\RequestBase');
        $mock->expects($this->once())->method('getScheme')->willReturn('http');
        $mock->expects($this->once())->method('getHost')->willReturn('somehost');

        \Request::setInstance($mock);
        $old_value = \Config::get('concrete.seo.canonical_url');
        \Config::set('concrete.seo.canonical_url', null);

        $this->assertEquals(
            (string) \Concrete\Core\Url\Url::createFromUrl("http://somehost")->setPath(\Core::getApplicationRelativePath()),
            (string) $this->urlResolver->resolve(array()));


        \Config::set('concrete.seo.canonical_url', $old_value);
    }

}
