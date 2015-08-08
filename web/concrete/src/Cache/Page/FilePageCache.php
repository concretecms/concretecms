<?php

namespace Concrete\Core\Cache\Page;
use Config;
use \Page as ConcretePage;
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
		$cacheKey = $this->getCacheKey($mixed);
		if ($cacheKey) {

			$varyOnRecord = $this->getVaryOnRecord($mixed);
			$cacheVaryOnKey = $this->getVaryOnCacheKey($varyOnRecord);

			$key = hash('sha256', $cacheKey . $cacheVaryOnKey);

			$filename = $key . '.cache';
			$dir = Config::get('concrete.cache.page.directory') . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
			if ($dir && (!is_dir($dir))) {
				@mkdir($dir, Config::get('concrete.filesystem.permissions.directory'), true);
			}
			$path = $dir . '/' . $filename;
			return $path;
		}
	}

	protected function getVaryOnRecord($mixed) {
		$file = $this->getVaryOnCacheFile($mixed);

		if (file_exists($file)) {
			$contents = file_get_contents($file);
			$record = @unserialize($contents);

			//TODO: VaryOnRecordClass?
			if (is_array($record)) {
				return $record;
			}
		}

		return array();
	}

	protected function getVaryOnCacheFile($mixed) {
		$cacheKey = $this->getCacheKey($mixed);
		if ($cacheKey) {
			$cacheKey = hash('sha256',$cacheKey);
			$filename = $cacheKey . '.cache';
			$dir = Config::get('concrete.cache.page.vary_on.directory') . '/' . $cacheKey[0] . '/' . $cacheKey[1] . '/' . $cacheKey[2];
			if ($dir && (!is_dir($dir))) {
				@mkdir($dir, Config::get('concrete.filesystem.permissions.directory'), true);
			}
			$path = $dir . '/' . $filename;
		}
		return $path;
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

	public function purge(ConcretePage $c) {
		$file = $this->getCacheFile($c);
		if ($file && file_exists($file)) {
			@unlink($file);
		}
	}

	public function set(ConcretePage $c, $content) {
		foreach(array(Config::get('concrete.cache.page.directory'), Config::get('concrete.cache.page.vary_on.directory')) as $dir) {
			if (!is_dir($dir)) {
				@mkdir($dir);
				@touch($dir . '/index.html');
			}
		}

		$varyOnSettingsFile = $this->getVaryOnCacheFile($c);
		if($varyOnSettingsFile) {
			$varyOnSettings = $this->getVaryOnCacheSettings($c);
			file_put_contents($varyOnSettingsFile, serialize($varyOnSettings));
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
