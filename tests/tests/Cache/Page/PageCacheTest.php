<?php

namespace Concrete\Tests\Cache\Page;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Locale\Service as LocaleService;
use Concrete\Core\Support\Facade\Facade;
use Core;
use PHPUnit_Framework_TestCase;

class PageCacheTest extends PHPUnit_Framework_TestCase
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

    public function testGetCacheHostForRequest()
    {
        $cache = PageCache::getLibrary();
        $request = Request::getInstance();

        $this->assertEquals(
            'www.requestdomain.com',
            $request->getHttpHost()
        );
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
        // Create a mock locale and return it from the mock locale service in
        // order to get the home page ID for the page cache key.
        $mockSiteTree = $this->getMockBuilder('Concrete\Core\Entity\Site\Tree')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSiteTree->expects($this->once())
            ->method('getSiteHomePageID')
            ->willReturn(123);
        $mockLocale = $this->getMockBuilder('Concrete\Core\Entity\Site\Locale')
            ->disableOriginalConstructor()
            ->getMock();
        $mockLocale->expects($this->once())
            ->method('getSiteTreeObject')
            ->willReturn($mockSiteTree);
        $ls->expects($this->once())
            ->method('getDefaultLocale')
            ->willReturn($mockLocale);

        $mockPage = $this->getMockBuilder('Concrete\Core\Page\Page')->getMock();
        $mockPage->expects($this->once())
            ->method('getSite')
            ->willReturn($mockSite);
        $mockPage->expects($this->once())
            ->method('getCollectionID')
            ->willReturn(123);

        $this->assertEquals(
            'www.concrete5.org!123',
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

        // Create a mock locale and return it from the mock locale service in
        // order to get the home page ID for the page cache key.
        $mockSiteTree = $this->getMockBuilder('Concrete\Core\Entity\Site\Tree')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSiteTree->expects($this->once())
            ->method('getSiteHomePageID')
            ->willReturn(123);
        $mockLocale = $this->getMockBuilder('Concrete\Core\Entity\Site\Locale')
            ->disableOriginalConstructor()
            ->getMock();
        $mockLocale->expects($this->once())
            ->method('getSiteTreeObject')
            ->willReturn($mockSiteTree);
        $ls->expects($this->once())
            ->method('getDefaultLocale')
            ->willReturn($mockLocale);

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
            'www.concrete5.org!123',
            $cache->getCacheKey($mockRequest)
        );

        // Revert to the defaults
        $app->bind(LocaleService::class, function () use ($origLs) {
            return $origLs;
        });
    }
}
