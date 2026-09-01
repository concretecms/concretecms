<?php

namespace Concrete\Tests\Cache\Page;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Locale\Service as LocaleService;
use Concrete\Core\Support\Facade\Facade;
use Core;
use Concrete\Tests\TestCase;

class PageCacheTest extends TestCase
{
    public function testGetCacheHostForPage()
    {
        $cache = PageCache::getLibrary();

        $mockSite = $this->getMockBuilder('Concrete\Core\Entity\Site\Site')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSite->expects($this->once())
            ->method('getSiteCanonicalURL')
            ->willReturn('http://www.concrete5.org');

        $mockPage = $this->getMockBuilder('Concrete\Core\Page\Page')->getMock();
        $mockPage->expects($this->once())
            ->method('getSite')
            ->willReturn($mockSite);

        $this->assertEquals(
            'www.concrete5.org',
            $cache->getCacheHost($mockPage)
        );

        $mockSite = $this->getMockBuilder('Concrete\Core\Entity\Site\Site')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSite->expects($this->once())
            ->method('getSiteCanonicalURL')
            ->willReturn('https://www.concrete5.org');

        $mockPage = $this->getMockBuilder('Concrete\Core\Page\Page')->getMock();
        $mockPage->expects($this->once())
            ->method('getSite')
            ->willReturn($mockSite);

        $this->assertEquals(
            'www.concrete5.org',
            $cache->getCacheHost($mockPage)
        );
    }

    public function testGetCacheHostForRequest(): void
    {
        $cache = PageCache::getLibrary();

        // The current request is the one defined in the tests bootstrap file.
        $request = Request::getInstance();
        static::assertEquals('www.requestdomain.com', $request->getHttpHost());
        static::assertEquals('www.requestdomain.com', $cache->getCacheHost($request));

        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->once())
            ->method('getHttpHost')
            ->willReturn('www.concrete5.org');

        static::assertEquals('www.concrete5.org', $cache->getCacheHost($mockRequest));
    }

    public function testGetCacheHostForUnrecognizedValue(): void
    {
        $cache = PageCache::getLibrary();

        static::assertNull($cache->getCacheHost(null));
        static::assertNull($cache->getCacheHost('www.concrete5.org'));
        static::assertNull($cache->getCacheHost(new \stdClass()));
    }

    public function testGetCacheKeyForPage()
    {
        $app = Facade::getFacadeApplication();

        // Temporarily bind a mock locale service in order to fetch the home
        // page ID.
        $origLs = Core::make(LocaleService::class);
        $ls = $this->getMockBuilder(LocaleService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $app->bind(LocaleService::class, function () use ($ls) {
            return $ls;
        });

        $cache = PageCache::getLibrary();

        // Test cache key with page path
        $mockSite = $this->getMockBuilder('Concrete\Core\Entity\Site\Site')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSite->expects($this->exactly(4))
            ->method('getSiteCanonicalURL')
            ->willReturn('http://www.concrete5.org');

        $mockPage = $this->getMockBuilder('Concrete\Core\Page\Page')->getMock();
        $mockPage->expects($this->exactly(3))
            ->method('getSite')
            ->willReturn($mockSite);
        $mockPage->expects($this->exactly(3))
            ->method('getCollectionPath')
            ->willReturn('/test/path');

        $this->assertEquals(
            'www.concrete5.org%2Ftest%2Fpath',
            $cache->getCacheKey($mockPage)
        );

        // Test cache key with page path + controller action
        // Then test with page path + controller action + request params
        $mockCtrl = $this->getMockBuilder(
            'Concrete\Core\Page\Controller\PageController'
        )->disableOriginalConstructor()->getMock();
        $mockCtrl->expects($this->exactly(2))
            ->method('getRequestAction')
            ->willReturn('action');
        $mockCtrl->expects($this->exactly(2))
            ->method('getRequestActionParameters')
            ->will($this->onConsecutiveCalls(
                [],
                ['p1', 'p2']
            ));
        $mockPage->expects($this->exactly(2))
            ->method('getPageController')
            ->willReturn($mockCtrl);

        $this->assertEquals(
            'www.concrete5.org%2Ftest%2Fpath%2Faction',
            $cache->getCacheKey($mockPage)
        );
        $this->assertEquals(
            'www.concrete5.org%2Ftest%2Fpath%2Faction%2Fp1%2Fp2',
            $cache->getCacheKey($mockPage)
        );

        // Test cache key with home page
        $mockPage = $this->getMockBuilder('Concrete\Core\Page\Page')->getMock();
        $mockPage->expects($this->once())
            ->method('getSite')
            ->willReturn($mockSite);
        $mockPage->method('isHomePage')
            ->willReturn(true);

        $this->assertEquals(
            'www.concrete5.org',
            $cache->getCacheKey($mockPage)
        );

        // Revert to the defaults
        $app->bind(LocaleService::class, function () use ($origLs) {
            return $origLs;
        });
    }

    public function testGetCacheKeyForRequest()
    {
        $app = Facade::getFacadeApplication();

        // Temporarily bind a mock locale service in order to fetch the home
        // page ID.
        $origLs = Core::make(LocaleService::class);
        $ls = $this->getMockBuilder(LocaleService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $app->bind(LocaleService::class, function () use ($ls) {
            return $ls;
        });

        $cache = PageCache::getLibrary();

        $mockRequest = $this->getMockBuilder('Concrete\Core\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRequest->expects($this->exactly(2))
            ->method('getHttpHost')
            ->willReturn('www.concrete5.org');
        $mockRequest->expects($this->exactly(2))
            ->method('getPath')
            ->will($this->onConsecutiveCalls(
                '/test/path',
                ''
            ));

        $this->assertEquals(
            'www.concrete5.org%2Ftest%2Fpath',
            $cache->getCacheKey($mockRequest)
        );
        $this->assertEquals(
            'www.concrete5.org',
            $cache->getCacheKey($mockRequest)
        );

        // Revert to the defaults
        $app->bind(LocaleService::class, function () use ($origLs) {
            return $origLs;
        });
    }
}
