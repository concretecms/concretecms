<?php

declare(strict_types=1);

namespace Concrete\Core\Cache\Page;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page as ConcretePage;

final class ConcretePageCache extends PageCache
{

    public function __construct(
        /**
         * @var PageCache<PageCacheRecord>
         */
        public readonly \Concrete\Core\Cache\Level\PageCache $cache,
        protected Repository $config,
    ) {}

    public function getRecord(Request|PageCacheRecord|ConcretePage $mixed): PageCacheRecord|null
    {
        $key = $this->getCacheKey($mixed);
        return $this->cache->pool->getItem($key)->get();
    }

    public function set(ConcretePage $c, string $content): void
    {
        $key = $this->getCacheKey($c);
        $record = new PageCacheRecord(
            $c,
            $content,
            $c->getCollectionFullPageCachingLifetimeValue(),
            null,
            $key,
            $this->getCacheHeaders($c),
        );
        $this->cache->pool->save($this->cache->pool->getItem($key)->set($record));
    }

    /**
     * @deprecated use ConcretePageCache::purge() instead
     * @see purge
     */
    public function purgeByRecord(PageCacheRecord $rec): void
    {
        $this->purge($rec);
    }

    public function purge(Request|PageCacheRecord|ConcretePage $c): void
    {
        $this->cache->pool->deleteItem($this->getCacheKey($c));
    }

    public function flush(): void
    {
        $this->config->save('concrete.cache.page.salt', bin2hex(random_bytes(16)));
        $this->cache->pool->clear();
    }

    public function getCacheKey(Request|PageCacheRecord|ConcretePage $mixed): string|null
    {
        return $this->config->get('concrete.cache.page.salt') . ';' . parent::getCacheKey($mixed);
    }
}