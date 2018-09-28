<?php

namespace Concrete\Core\Cache\Page;

use Concrete\Core\Cache\FlushableInterface;
use Concrete\Core\Http\Response;
use Config;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page as ConcretePage;
use \Concrete\Core\Page\View\PageView;
use Permissions;
use User;
use Session;
use Concrete\Core\Support\Facade\Application;

abstract class PageCache implements FlushableInterface
{

    /**
     * @var \Concrete\Core\Cache\Page\PageCache|null
     */
    static $library;

    /**
     * Build a Response object starting from a cached page.
     *
     * @param \Concrete\Core\Cache\Page\PageCacheRecord $record The cache record containing the cached page data.
     *
     * @return \Concrete\Core\Http\Response
     */
    public function deliver(PageCacheRecord $record)
    {
        $response = new Response();
        $headers = array();
        if (defined('APP_CHARSET')) {
            $headers["Content-Type"] = "text/html; charset=" . APP_CHARSET;
        }
        $headers = array_merge($headers, $record->getCacheRecordHeaders());

        $response->headers->add($headers);
        $response->setContent($record->getCacheRecordContent());
        return $response;
    }

    /**
     * Get the page cache library.
     *
     * @return \Concrete\Core\Cache\Page\PageCache
     */
    public static function getLibrary()
    {
        if (!PageCache::$library) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $adapter = $config->get('concrete.cache.page.adapter');
            $class = overrideable_core_class(
                'Core\\Cache\\Page\\' . camelcase($adapter) . 'PageCache',
                DIRNAME_CLASSES . '/Cache/Page/' . camelcase($adapter) . 'PageCache.php'
            );
            PageCache::$library = $app->build($class);
        }
        return PageCache::$library;
    }

    /**
     * Determine if we should check if a page is in the cache.
     *
     * @param \Concrete\Core\Http\Request $req
     *
     * @return bool
     */
    public function shouldCheckCache(Request $req)
    {
        if ($req->isPost()) {
            return false;
        }
        
        $app = \Core::make('app');
        $config = $app['config'];
        $cookie = $app['cookie'];
        $loginCookie = sprintf('%s_LOGIN', $config->get('concrete.session.name'));
        if ($cookie->has($loginCookie) && $cookie->get($loginCookie)) {
            return false;
        }
        return true;
    }

    /**
     * Send the cache-related HTTP headers for a page to the current response.
     *
     * @deprecated Response headers should be set by the deliver method.
     *
     * @param \Concrete\Core\Page\Page $c
     */
    public function outputCacheHeaders(ConcretePage $c)
    {
        foreach ($this->getCacheHeaders($c) as $header) {
            header($header);
        }
    }

    /**
     * Get the cache-related HTTP headers for a page.
     *
     * @param \Concrete\Core\Page\Page $c
     *
     * @return array Keys are the header names; values are the header values
     */
    public function getCacheHeaders(ConcretePage $c)
    {
        $lifetime = $c->getCollectionFullPageCachingLifetimeValue();
        $expires = gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT';

        $headers = array(
            'Pragma' => 'public',
            'Cache-Control' => 'max-age=' . $lifetime . ',s-maxage=' . $lifetime,
            'Expires' => $expires
        );

        return $headers;
    }

    /**
     * Check if a page contained in a PageView should be stored in the cache.
     *
     * @param \Concrete\Core\Page\View\PageView $v
     *
     * @return bool
     */
    public function shouldAddToCache(PageView $v)
    {
        $c = $v->getCollectionObject();
        if (!is_object($c)) {
            return false;
        }

        $cp = new Permissions($c);
        if (!$cp->canViewPage()) {
            return false;
        }

        $u = new User();

        $allowedControllerActions = array('view');
        if (is_object($v->controller)) {
            if (!in_array($v->controller->getTask(), $allowedControllerActions)) {
                return false;
            }
        }

        if (!$c->getCollectionFullPageCaching()) {
            return false;
        }

        if ($u->isRegistered() || $_SERVER['REQUEST_METHOD'] == 'POST') {
            return false;
        }

        if ($c->isGeneratedCollection()) {
            if ((is_object($v->controller) && (!$v->controller->supportsPageCache())) || (!is_object($v->controller))) {
                return false;
            }
        }

        if ($c->getCollectionFullPageCaching() == 1 || Config::get('concrete.cache.pages') === 'all') {
            // this cache page at the page level
            // this overrides any global settings
            return true;
        }

        if (Config::get('concrete.cache.pages') !== 'blocks') {
            // we are NOT specifically caching this page, and we don't
            return false;
        }

        $blocks = $c->getBlocks();
        $blocks = array_merge($c->getGlobalBlocks(), $blocks);

        foreach ($blocks as $b) {
            if (!$b->cacheBlockOutput()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the key that identifies the cache entry for a page or a request.
     *
     * @param \Concrete\Core\Page\Page|\Concrete\Core\Http\Request|mixed $mixed
     *
     * @return string|null Returns NULL if $mixed is not a recognized type, a string otherwise
     */
    public function getCacheKey($mixed)
    {
        $homeCID = 1;
        if ($mixed instanceof ConcretePage) {
            if ($mixed->getCollectionPath() != '') {
                return urlencode(trim($mixed->getCollectionPath(), '/'));
            } else {
                if ($mixed->getCollectionID() == $homeCID) {
                    return '!' . $homeCID;
                }
            }
        } else {
            if ($mixed instanceof \Concrete\Core\Http\Request) {
                if ($mixed->getPath() != '') {
                    return urlencode(trim($mixed->getPath(), '/'));
                } else {
                    return '!' . $homeCID;
                }
            } else {
                if ($mixed instanceof PageCacheRecord) {
                    return $mixed->getCacheRecordKey();
                }
            }
        }
    }

    /**
     * Get the cached item for a page or a request.
     *
     * @param \Concrete\Core\Page\Page|\Concrete\Core\Http\Request|mixed $mixed
     *
     * @return \Concrete\Core\Cache\Page\PageCacheRecord|null Return NULL if $mixed is not a recognized type, or if it's the record is not in the cache
     */
    abstract public function getRecord($mixed);

    /**
     * Store a page in the cache.
     *
     * @param \Concrete\Core\Page\Page $c The page to be stored in the cache
     * @param string $content The whole HTML of the page to be stored in the cache
     */
    abstract public function set(ConcretePage $c, $content);

    /**
     * Remove a cache entry given the record retrieved from the cache.
     *
     * @param \Concrete\Core\Cache\Page\PageCacheRecord $rec
     */
    abstract public function purgeByRecord(\Concrete\Core\Cache\Page\PageCacheRecord $rec);

    /**
     * Remove a cache entry given the page.
     *
     * @param \Concrete\Core\Cache\Page\PageCacheRecord $rec
     */
    abstract public function purge(ConcretePage $c);

    /**
     * {@inheritDoc}
     *
     * @see \Concrete\Core\Cache\FlushableInterface::flush()
     */
    abstract public function flush();

}
