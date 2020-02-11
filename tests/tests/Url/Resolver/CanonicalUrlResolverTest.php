<?php

namespace Concrete\Tests\Url\Resolver;

use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\CreateClassMockTrait;
use Concrete\TestHelpers\Url\Resolver\ResolverTestCase;

class CanonicalUrlResolverTest extends ResolverTestCase
{
    use CreateClassMockTrait;

    public function testConfig()
    {
        $app = Application::getFacadeApplication();
        $resolver = new \Concrete\Core\Url\Resolver\CanonicalUrlResolver(
            $app,
            \Core::make('Concrete\Core\Http\Request'));

        $canonical = 'http://example.com:1337';
        $siteConfig = $app->make('site')->getSite()->getConfigRepository();

        $old_value = $siteConfig->get('seo.canonical_url');
        $siteConfig->set('seo.canonical_url', $canonical);

        try {
            $this->assertEquals(
                (string) \Concrete\Core\Url\Url::createFromUrl($canonical)->setPath(\Core::getApplicationRelativePath()),
                (string) $resolver->resolve([]));
        } finally {
            $siteConfig->set('seo.canonical_url', $old_value);
        }
    }

    public function testFromRequest()
    {
        $app = Application::getFacadeApplication();
        $mock = $this->createMockFromClass('Concrete\Core\Http\Request');
        $mock->expects($this->once())->method('getScheme')->willReturn('http');
        $mock->expects($this->once())->method('getHost')->willReturn('somehost');

        $resolver = new \Concrete\Core\Url\Resolver\CanonicalUrlResolver($app, $mock);

        $siteConfig = $app->make('site')->getSite()->getConfigRepository();
        $old_value = $siteConfig->get('seo.canonical_url');
        $siteConfig->set('seo.canonical_url', null);

        try {
            $this->assertEquals(
                (string) \Concrete\Core\Url\Url::createFromUrl('http://somehost')->setPath(\Core::getApplicationRelativePath()),
                (string) $resolver->resolve([]));
        } finally {
            $siteConfig->set('seo.canonical_url', $old_value);
        }
    }
}
