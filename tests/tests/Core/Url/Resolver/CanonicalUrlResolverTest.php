<?php

require_once __DIR__ . "/ResolverTestCase.php";

class CanonicalUrlResolverTest extends ResolverTestCase
{

    public function testConfig()
    {
        $resolver = new \Concrete\Core\Url\Resolver\CanonicalUrlResolver(
            \Core::getFacadeApplication(),
            \Core::make('Concrete\Core\Http\Request'));

        $canonical = "http://example.com:1337";

        $old_value = \Config::get('concrete.seo.canonical_url');
        \Config::set('concrete.seo.canonical_url', $canonical);

        $this->assertEquals(
            (string) \Concrete\Core\Url\Url::createFromUrl($canonical)->setPath(\Core::getApplicationRelativePath()),
            (string) $resolver->resolve(array()));

        \Config::set('concrete.seo.canonical_url', $old_value);
    }

    public function testFromRequest()
    {
        $mock = $this->getMock('Concrete\Core\Http\Request');
        $mock->method('getScheme')->willReturn('http');
        $mock->method('getHost')->willReturn('somehost');

        $resolver = new \Concrete\Core\Url\Resolver\CanonicalUrlResolver(\Core::getFacadeApplication(), $mock);

        $old_value = \Config::get('concrete.seo.canonical_url');
        \Config::set('concrete.seo.canonical_url', null);
        $app = \Core::make('app');
        $app['app_relative_path'] = '';


        $this->assertEquals(
            (string) \Concrete\Core\Url\Url::createFromUrl("/")->setPath(\Core::getApplicationRelativePath()),
            (string) $resolver->resolve(array()));


        \Config::set('concrete.seo.canonical_url', $old_value);
    }

}
