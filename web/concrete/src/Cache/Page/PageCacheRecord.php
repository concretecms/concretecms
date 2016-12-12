<?php
namespace Concrete\Core\Cache\Page;

use Concrete\Core\Page\Page;

class PageCacheRecord {

	public function __construct(Page $c, $content, $lifetime) {
		$cache = PageCache::getLibrary();
		$this->setCacheRecordLifetime($lifetime);
		$this->setCacheRecordKey($cache->getCacheKey($c));
		$this->setCacheRecordHeaders($cache->getCacheHeaders($c));
		$this->setCacheRecordContent($content);
	}

	public function setCacheRecordLifetime($lifetime) {
		$this->expires = time() + $lifetime;
	}

	public function getCacheRecordExpiration() {
		return $this->expires;
	}

	public function setCacheRecordContent($content) {
		$this->content = $content;
	}

	public function getCacheRecordContent() {
		return $this->content;
	}

	public function setCacheRecordHeaders($headers) {
		$this->headers = $headers;
	}

	public function getCacheRecordHeaders() {
		return $this->headers;
	}

	public function getCacheRecordKey() {
		return $this->cacheRecordKey;
	}

	public function setCacheRecordKey($cacheRecordKey) {
		$this->cacheRecordKey = $cacheRecordKey;
	}

	public function validate() {
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
