<?php

namespace Concrete\Core\Cache\Page;
use Concrete\Core\Page\Page;
use Config;
use Loader;

class FilePageCache extends PageCache {

	public function getRecord($mixed) {
		$file = $this->getCacheFile($mixed);
		if (file_exists($file)) {
			$contents = file_get_contents($file);
			$record = @unserialize($contents);
			if ($record instanceof PageCacheRecord) {
				return $record;
			}
		}
	}

	protected function getCacheFile($mixed) {
		$key = $this->getCacheKey($mixed);
		if ($key) {
			$key = hash('sha256',$key);
			$filename = $key . '.cache';
			$dir = Config::get('concrete.cache.page.directory') . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
			if ($dir && (!is_dir($dir))) {
				@mkdir($dir, Config::get('concrete.filesystem.permissions.directory'), true);
			}
			$path = $dir . '/' . $filename;
			return $path;
		}
	}

	public function purgeByRecord(\Concrete\Core\Cache\Page\PageCacheRecord $rec) {
		$file = $this->getCacheFile($rec);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function flush() {
		$fh = Loader::helper('file');
		$fh->removeAll(Config::get('concrete.cache.page.directory'));
	}

	public function purge(Page $c) {
		$file = $this->getCacheFile($c);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function set(Page $c, $content) {
		if (!is_dir(Config::get('concrete.cache.page.directory'))) {
			@mkdir(Config::get('concrete.cache.page.directory'));
			@touch(Config::get('concrete.cache.page.directory') . '/index.html');
		}
		$lifetime = $c->getCollectionFullPageCachingLifetimeValue();
		$file = $this->getCacheFile($c);
		if ($file) {
			$response = new PageCacheRecord($c, $content, $lifetime);
			if ($content) {
				file_put_contents($file, serialize($response));
			}
		}
	}


}
