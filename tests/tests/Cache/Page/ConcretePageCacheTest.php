<?php

namespace Concrete\Tests\Cache\Page;

use Concrete\Core\Api\Model\Site;
use Concrete\Core\Cache\Level\PageCache;
use Concrete\Core\Cache\Page\ConcretePageCache;
use Concrete\Core\Cache\Page\PageCacheRecord;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Before;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\CacheItem;

class ConcretePageCacheTest extends TestCase
{

    protected ConcretePageCache|MockInterface $cache;
    protected CacheItemPoolInterface|MockInterface $mockPool;
    protected PageCache|MockInterface $mockCache;
    protected Repository|MockInterface $mockConfig;
    private Page|MockInterface $mockPage;

    /**
     * @before
     */
    public function before(): void
    {
        $this->mockPool = Mockery::mock(CacheItemPoolInterface::class);
        $this->mockCache = new PageCache($this->mockPool);
        $this->mockConfig = Mockery::mock(Repository::class);
        $this->mockConfig
            ->shouldReceive('get')
            ->with('concrete.cache.page.salt')
            ->andReturn('foo')
            ->byDefault();

        $this->cache = new ConcretePageCache($this->mockCache, $this->mockConfig);

        $mockPage = Mockery::mock(Page::class);
        $mockPage->shouldReceive('getSite')->andReturn(null);
        $mockPage->shouldReceive('getCollectionPath')->andReturn('/foo/baz');
        $mockPage->shouldReceive('getPageController')->andReturn(null);
        $this->mockPage = $mockPage;
    }

    public function testGetRecord(): void
    {
        $fakeRecord = Mockery::mock(PageCacheRecord::class);

        $fakeItem = new CacheItem();
        $fakeItem->set($fakeRecord);
        $this->mockPool->shouldReceive('getItem')->with('foo;www.requestdomain.com%2Ffoo%2Fbaz')->andReturn($fakeItem);

        $this->assertEquals($fakeRecord, $this->cache->getRecord($this->mockPage));
    }

    public function testSet(): void
    {
        $this->mockPage->expects('getCollectionFullPageCachingLifetimeValue')->zeroOrMoreTimes()
            ->andReturn(50);
        $fakeItem = new CacheItem();
        $this->mockPool->expects('getItem')->with('foo;www.requestdomain.com%2Ffoo%2Fbaz')->andReturn($fakeItem);

        $this->mockPool->expects('save')->with($fakeItem);

        $content = 'foo baz bar';
        $this->cache->set($this->mockPage, $content);

        $this->assertEquals('foo baz bar', $fakeItem->get()->getCacheRecordContent());
    }

    public function testPurgeRecord(): void
    {
        $record = Mockery::mock(PageCacheRecord::class);
        $record->expects('getCacheRecordKey')->andReturn('bar');
        $this->mockPool->expects('deleteItem')->with('foo;bar');

        $this->cache->purgeByRecord($record);

        $record->expects('getCacheRecordKey')->andReturn('bar');
        $this->mockPool->expects('deleteItem')->with('foo;bar');
        $this->cache->purge($record);
    }

    public function testPurge(): void
    {
        // Request
        $request = Mockery::mock(Request::class);
        $request->expects('getHttpHost')->andReturn('example.com');
        $request->expects('getPath')->andReturn('/some/path');
        $this->mockPool->expects('deleteItem')->with('foo;example.com%2Fsome%2Fpath');
        $this->cache->purge($request);

        // Basic page
        $page = Mockery::mock(Page::class);
        $page->expects('getSite')->andReturn(null);
        $page->expects('getCollectionPath')->andReturn('/some/path');
        $page->expects('getPageController')->andReturn(null);
        $this->mockPool->expects('deleteItem')->with('foo;www.requestdomain.com%2Fsome%2Fpath');
        $this->cache->purge($page);

        // With canonical url
        $page = Mockery::mock(Page::class);
        $page->expects('getSite->getSiteCanonicalURL')->andReturn('https://example.com/');
        $page->expects('getCollectionPath')->andReturn('/some/path');
        $page->expects('getPageController')->andReturn(null);
        $this->mockPool->expects('deleteItem')->with('foo;example.com%2Fsome%2Fpath');
        $this->cache->purge($page);

        // With action
        $page = Mockery::mock(Page::class);
        $page->expects('getSite->getSiteCanonicalURL')->andReturn('https://example.com/');
        $page->expects('getPageController->getRequestAction')->andReturn('foo');
        $page->expects('getPageController->getRequestActionParameters')->andReturn(['baz', 'bar']);
        $page->expects('getCollectionPath')->andReturn('/some/path');
        $this->mockPool->expects('deleteItem')->with('foo;example.com%2Fsome%2Fpath%2Ffoo%2Fbaz%2Fbar');
        $this->cache->purge($page);
    }

    public function testFlush(): void
    {
        $this->mockConfig->expects('save')->withArgs(function(string $key, string $value) {
            $this->assertEquals(32, strlen($value));
            return true;
        });
        $this->mockPool->expects('clear');
        $this->cache->flush();
    }

    public function testGetCacheKey(): void
    {
        $salt = bin2hex(random_bytes(12));
        $this->mockConfig->shouldReceive('get')
            ->with('concrete.cache.page.salt')->andReturn($salt);

        $record = Mockery::mock(PageCacheRecord::class);
        $record->expects('getCacheRecordKey')->andReturn('foo');

        $key = $this->cache->getCacheKey($record);
        $this->assertEquals("{$salt};foo", $key);
    }
}