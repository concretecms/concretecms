<?php
namespace Concrete\Core\Cache\Page;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Url;

class PageCacheRecord
{
    public function __construct(Page $c, $content, $lifetime, $url = null)
    {
        $cache = PageCache::getLibrary();
        $this->setCacheRecordLifetime($lifetime);
        $this->setCacheRecordKey($cache->getCacheKey($c));
        $this->setCacheRecordHeaders($cache->getCacheHeaders($c));
        $this->setCanonicalURL($url);
        $this->setCacheRecordContent($content);
    }

    public function setCacheRecordLifetime($lifetime)
    {
        $this->expires = time() + $lifetime;
    }

    public function getCanonicalURL()
    {
        return $this->url;
    }

    public function setCanonicalURL($url)
    {
        $this->url = $url;
    }

    public function getCacheRecordExpiration()
    {
        return $this->expires;
    }

    public function setCacheRecordContent($content)
    {
        $this->content = $content;
    }

    public function getCacheRecordContent()
    {
        return $this->content;
    }

    public function setCacheRecordHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getCacheRecordHeaders()
    {
        return $this->headers;
    }

    public function getCacheRecordKey()
    {
        return $this->cacheRecordKey;
    }

    public function setCacheRecordKey($cacheRecordKey)
    {
        $this->cacheRecordKey = $cacheRecordKey;
    }

    public function validate(Request $request)
    {
        if ($this->getCanonicalURL()) {
            $url = Url::createFromUrl($this->getCanonicalURL());
            if ($url->getBaseUrl() != $request->getSchemeAndHttpHost()) {
                return false; // but don't invalidate
            }
        }

        $diff = $this->expires - time();
        if ($diff > 0) {
            // it's still valid
            return true;
        } else {
            // invalidate and kill this record.
            $cache = PageCache::getLibrary();
            $cache->purgeByRecord($this);
        }
    }
}
