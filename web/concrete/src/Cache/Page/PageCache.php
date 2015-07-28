<?php
namespace Concrete\Core\Cache\Page;

use Core;
use Concrete\Core\Http\Response;
use Config;
use Request;
use \Page as ConcretePage;
use \Concrete\Core\Page\View\PageView;
use Permissions;
use User;
use Session;

class PageCache
{

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

    public static function getLibrary()
    {
        return new static();
    }

    /**
     * Note: can't use the User object directly because it might query the database
     */
    public function shouldCheckCache(Request $req)
    {
        if (Session::get('uID') > 0) {
            return false;
        }
        return true;
    }

    public function outputCacheHeaders(ConcretePage $c)
    {
        foreach ($this->getCacheHeaders($c) as $header) {
            header($header);
        }
    }

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

    public function shouldAddToCache(PageView $v)
    {
        /** @var \Page $c */
        $c = $v->getPageObject();
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

        /** @var \Block[] $blocks */
        $blocks = $c->getBlocks();
        array_merge($c->getGlobalBlocks(), $blocks);

        foreach ($blocks as $b) {
            if (!$b->cacheBlockOutput()) {
                return false;
            }
        }
        return true;
    }

    public function getCacheKey($mixed)
    {
        if ($mixed instanceof ConcretePage) {
            if ($mixed->getCollectionPath() != '') {
                return urlencode(trim($mixed->getCollectionPath(), '/'));
            } else {
                if ($mixed->getCollectionID() == HOME_CID) {
                    return '!' . HOME_CID;
                }
            }
        } else {
            if ($mixed instanceof \Concrete\Core\Http\Request) {
                if ($mixed->getPath() != '') {
                    return urlencode(trim($mixed->getPath(), '/'));
                } else {
                    return '!' . HOME_CID;
                }
            } else {
                if ($mixed instanceof PageCacheRecord) {
                    return $mixed->getCacheRecordKey();
                }
            }
        }

        return null;
    }

    public function getRecord($mixed)
    {
        $key = $this->getCacheKey($mixed);
        $cache = Core::make('cache/page');
        $item = $cache->getItem($key);
        if (!$item->isMiss()) {
            return $item->get();
        }
        return null;
    }

    public function set(ConcretePage $c, $content)
    {
        $lifetime = $c->getCollectionFullPageCachingLifetimeValue();
        if ($content) {
            $key = $this->getCacheKey($c);
            $cache = Core::make('cache/page');
            $item = $cache->getItem($key);
            $response = new PageCacheRecord($c, $content, $lifetime);
            $item->set($response, $lifetime);
        }
    }

    public function purgeByRecord(\Concrete\Core\Cache\Page\PageCacheRecord $rec)
    {
        $key = $this->getCacheKey($rec);
        $cache = Core::make('cache/page');
        $item = $cache->getItem($key);
        $item->clear();
    }

    public function flush()
    {
        $cache = Core::make('cache/page');
        $cache->flush();
    }

    public function purge(ConcretePage $c)
    {

        $key = $this->getCacheKey($c);
        $cache = Core::make('cache/page');
        $item = $cache->getItem($key);
        $item->clear();
    }
}
