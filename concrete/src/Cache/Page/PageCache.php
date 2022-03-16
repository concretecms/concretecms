<?php

namespace Concrete\Core\Cache\Page;

use Concrete\Core\Cache\FlushableInterface;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\View\PageView;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

abstract class PageCache implements FlushableInterface
{
    /**
     * @deprecated what's deprecated is the "public" part: use the getLibrary() method to retrieve the library
     *
     * @var \Concrete\Core\Cache\Page\PageCache|null
     */
    public static $library;

    /**
     * Build a Response object starting from a cached page.
     *
     * @param \Concrete\Core\Cache\Page\PageCacheRecord $record the cache record containing the cached page data
     *
     * @return \Concrete\Core\Http\Response
     */
    public function deliver(PageCacheRecord $record)
    {
        $response = new Response();
        $headers = [];
        if (defined('APP_CHARSET')) {
            $headers['Content-Type'] = 'text/html; charset=' . APP_CHARSET;
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
        if (!self::$library) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $adapter = $config->get('concrete.cache.page.adapter');
            $class = overrideable_core_class(
                'Core\\Cache\\Page\\' . camelcase($adapter) . 'PageCache',
                DIRNAME_CLASSES . '/Cache/Page/' . camelcase($adapter) . 'PageCache.php'
            );
            self::$library = $app->build($class);
        }

        return self::$library;
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
        if ($req->getMethod() === $req::METHOD_POST) {
            return false;
        }

        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $cookie = $app->make('cookie');
        $loginCookie = sprintf('%s_LOGIN', $config->get('concrete.session.name'));
        if ($cookie->has($loginCookie) && $cookie->get($loginCookie)) {
            return false;
        }

        return true;
    }

    /**
     * Send the cache-related HTTP headers for a page to the current response.
     *
     * @deprecated Headers are no longer set directly. Instead, use the
     * <code>$pageCache->deliver()</code>
     * method to retrieve a response object and either return it from a controller method or, if you must, use
     * <code>$response->prepare($request)->send()</code>
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

        $headers = [
            'Pragma' => 'public',
            'Cache-Control' => 'max-age=' . $lifetime . ',s-maxage=' . $lifetime,
            'Expires' => $expires,
        ];

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
        $c = $v->getPageObject();
        if (!is_object($c)) {
            return false;
        }

        $cp = new Checker($c);
        if (!$cp->canViewPage()) {
            return false;
        }

        if (is_object($v->controller)) {
            $allowedControllerActions = ['view'];
            if (!in_array($v->controller->getAction(), $allowedControllerActions)) {
                return false;
            }
            if ($c->isGeneratedCollection() && !$v->controller->supportsPageCache()) {
                return false;
            }
        }

        if (!$c->getCollectionFullPageCaching()) {
            return false;
        }

        $app = Application::getFacadeApplication();
        $request = $app->make(Request::class);
        if ($request->getMethod() === $request::METHOD_POST) {
            return false;
        }

        $u = $app->make(User::class);
        if ($u->isRegistered()) {
            return false;
        }

        $config = $app->make('config');
        if ($c->getCollectionFullPageCaching() == 1 || $config->get('concrete.cache.pages') === 'all') {
            // this cache page at the page level
            // this overrides any global settings
            return true;
        }

        if ($config->get('concrete.cache.pages') !== 'blocks') {
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
     * @param \Concrete\Core\Page\Page|\Concrete\Core\Http\Request|\Concrete\Core\Cache\Page\PageCacheRecord|mixed $mixed
     *
     * @return string|null Returns NULL if $mixed is not a recognized type, a string otherwise
     */
    public function getCacheKey($mixed)
    {
        if ($mixed instanceof ConcretePage) {
            $host = $this->getCacheHost($mixed);
            if (empty($host)) {
                // Default to the request host. This should only happen in case
                // the canonical URL has not been set for the site that the page
                // belongs to.
                $host = Request::getInstance()->getHttpHost();
            }

            $collectionPath = (string) $mixed->getCollectionPath();

            // Add the "extra" parts to the path that can be added to the URL
            // because the page/page type controller can have request actions.
            $ctrl = $mixed->getPageController();
            if (is_object($ctrl) &&
                !empty($action = $ctrl->getRequestAction())
            ) {
                $extra = [];
                if ($action !== 'view') {
                    $extra[] = $action;
                }
                $extra = array_merge(
                    $extra,
                    $ctrl->getRequestActionParameters()
                );

                if (count($extra) > 0) {
                    $collectionPath .= '/' . implode('/', $extra);
                }
            }

            $collectionPath = trim($collectionPath, '/');
            if ($collectionPath !== '') {
                return urlencode($host . '/' . $collectionPath);
            }
            if ($mixed->isHomePage()) {
                return urlencode($host);
            }
        } elseif ($mixed instanceof Request) {
            $host = $this->getCacheHost($mixed);
            $path = trim((string) $mixed->getPath(), '/');
            if ($path !== '') {
                return urlencode($host . '/' . $path);
            }

            return urlencode($host);
        } elseif ($mixed instanceof PageCacheRecord) {
            return $mixed->getCacheRecordKey();
        }
    }

    /**
     * Get the host name under which the page or request belongs to.
     *
     * @param \Concrete\Core\Page\Page|\Concrete\Core\Http\Request|mixed $mixed
     *
     * @return string|null Returns NULL if $mixed is not a recognized type, a string otherwise
     */
    public function getCacheHost($mixed)
    {
        if ($mixed instanceof ConcretePage) {
            $site = $mixed->getSite();
            if (is_object($site)) {
                $host = $site->getSiteCanonicalURL();
                if (!empty($host)) {
                    $host = preg_replace('#^https?://#', '', $host);
                    $host = trim($host, '/'); // Do not want trailing slashes in the host.
                    return $host;
                }
            }
        } elseif ($mixed instanceof Request) {
            return $mixed->getHttpHost();
        }

        return null;
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
    abstract public function purgeByRecord(PageCacheRecord $rec);

    /**
     * Remove a cache entry given the page.
     *
     * @param \Concrete\Core\Cache\Page\PageCacheRecord $rec
     * @param ConcretePage $c
     */
    abstract public function purge(ConcretePage $c);

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Cache\FlushableInterface::flush()
     */
    abstract public function flush();
}
