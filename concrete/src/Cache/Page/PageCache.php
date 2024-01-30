<?php

namespace Concrete\Core\Cache\Page;

use Concrete\Core\Cache\FlushableInterface;
use Concrete\Core\Entity\Site\Site;
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
     * @var PageCache|null
     */
    public static PageCache|null $library = null;

    /**
     * Build a Response object starting from a cached page.
     *
     * @param PageCacheRecord $record the cache record containing the cached page data
     *
     * @return Response
     */
    public function deliver(PageCacheRecord $record): Response
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
     * @return PageCache
     */
    public static function getLibrary(): PageCache
    {
        if (!self::$library) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $adapter = $config->get('concrete.cache.page.adapter');
            $class = overrideable_core_class(
                'Core\\Cache\\Page\\' . camelcase($adapter) . 'PageCache',
                DIRNAME_CLASSES . '/Cache/Page/' . camelcase($adapter) . 'PageCache.php'
            );

            self::$library = $app->make($class);
        }

        return self::$library;
    }

    /**
     * Determine if we should check if a page is in the cache.
     *
     * @param Request $req
     *
     * @return bool
     */
    public function shouldCheckCache(Request $req): bool
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
     * @param ConcretePage $c
     *@deprecated Headers are no longer set directly. Instead, use the
     * <code>$pageCache->deliver()</code>
     * method to retrieve a response object and either return it from a controller method or, if you must, use
     * <code>$response->prepare($request)->send()</code>
     *
     */
    public function outputCacheHeaders(ConcretePage $c): void
    {
        foreach ($this->getCacheHeaders($c) as $header) {
            header($header);
        }
    }

    /**
     * Get the cache-related HTTP headers for a page.
     *
     * @param ConcretePage $c
     *
     * @return array<string, string> Keys are the header names; values are the header values
     */
    public function getCacheHeaders(ConcretePage $c): array
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
     * @param PageView $v
     *
     * @return bool
     */
    public function shouldAddToCache(PageView $v): bool
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
     * @param ConcretePage|Request|PageCacheRecord|mixed $mixed
     *
     * @return string|null Returns NULL if $mixed is not a recognized type, a string otherwise
     */
    public function getCacheKey(ConcretePage|Request|PageCacheRecord $mixed): string|null
    {
        if ($mixed instanceof PageCacheRecord) {
            return $mixed->getCacheRecordKey();
        }

        if ($mixed instanceof Request) {
            $host = $this->getCacheHost($mixed);
            $path = trim((string) $mixed->getPath(), '/');
            if ($path !== '') {
                return urlencode($host . '/' . $path);
            }

            return urlencode($host);
        }

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
        if ($ctrl && is_object($ctrl) && !empty($action = $ctrl->getRequestAction())) {
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

        return null;
    }

    /**
     * Get the host name under which the page or request belongs to.
     *
     * @param ConcretePage|Request $mixed
     *
     * @return string|null Returns NULL if $mixed is not a recognized type, a string otherwise
     */
    public function getCacheHost(ConcretePage|Request $mixed): string|null
    {
        if ($mixed instanceof Request) {
            return $mixed->getHttpHost();
        }

        $site = $mixed->getSite();
        if ($site !== null) {
            $host = $site->getSiteCanonicalURL();
            if (!empty($host)) {
                $host = preg_replace('#^https?://#', '', $host);
                $host = trim($host, '/'); // Do not want trailing slashes in the host.
                return $host;
            }
        }

        return null;
    }

    /**
     * Get the cached item for a page or a request.
     *
     * @param ConcretePage|Request|mixed $mixed
     *
     * @return PageCacheRecord|null Return NULL if $mixed is not a recognized type, or if it's the record is not in the cache
     */
    abstract public function getRecord(ConcretePage|Request|PageCacheRecord $mixed): PageCacheRecord|null;

    /**
     * Store a page in the cache.
     *
     * @param ConcretePage $c The page to be stored in the cache
     * @param string $content The whole HTML of the page to be stored in the cache
     */
    abstract public function set(ConcretePage $c, string $content): void;

    /**
     * Remove a cache entry given the record retrieved from the cache.
     *
     * @param PageCacheRecord $rec
     */
    abstract public function purgeByRecord(PageCacheRecord $rec): void;

    /**
     * Remove a cache entry given the page.
     *
     * @param ConcretePage $c
     */
    abstract public function purge(ConcretePage $c): void;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Cache\FlushableInterface::flush()
     */
    abstract public function flush(): void;
}
