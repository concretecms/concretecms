<?php
namespace Concrete\Core\Cache\Page;


use Concrete\Core\Logging\Channels;
use Stash\Pool;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Cache\Driver\RedisStashDriver as Redis;

/**
 * Class RedisPageCache
 * @author Derek Cameron <info@derekcameron.com>
 */
class RedisPageCache extends PageCache
{
    /**
     * @var Pool
     */
    public static $pool;

    /**
     * RedisPageCache constructor.
     */
    public function __construct()
    {
        $app = Facade::getFacadeApplication();
        $driver = new Redis($app['config']->get('concrete.cache.page.redis'));
        self::$pool = new Pool($driver);
        self::$pool->setLogger($app->make('log/factory')->createLogger(Channels::CHANNEL_APPLICATION));
    }

    /**
     * @param \Concrete\Core\Http\Request|ConcretePage|mixed $mixed
     * @return PageCacheRecord|mixed|null
     */
    public function getRecord($mixed)
    {
        $item = $this->getCacheItem($mixed);
        
        if ($item !== null) {
            $record = $item->get();

            if ($record instanceof PageCacheRecord) {
                return $record;
            }
        }
    }

    /**
     * @param ConcretePage $c
     * @param string $content
     */
    public function set(ConcretePage $c, $content)
    {
        if ($content) {
            $item = $this->getCacheItem($c);

            if ($item !== null) {
                // Let other processes know that this one is rebuilding the data.
                $item->lock();

                $lifetime = $c->getCollectionFullPageCachingLifetimeValue();
                $response = new PageCacheRecord($c, $content, $lifetime);
                $item->set($response);
                self::$pool->save($item);
            }
        }
    }

    /**
     * @param PageCacheRecord $rec
     */
    public function purgeByRecord(PageCacheRecord $rec)
    {
        $item = $this->getCacheItem($rec);
        if ($item !== null) {
            $item->clear();
        }
    }

    /**
     * @param ConcretePage $c
     */
    public function purge(ConcretePage $c)
    {
        $item = $this->getCacheItem($c);
        if ($item !== null) {
            $item->clear();
        }
    }

    /**
     *
     */
    public function flush()
    {
        self::$pool->clear();
    }

    /**
     * @param $mixed
     * @return \Stash\Interfaces\ItemInterface
     */
    protected function getCacheItem($mixed)
    {
        $key = $this->getCacheKey($mixed);
        if ($key) {
            return self::$pool->getItem($key);
        }
    }
}
