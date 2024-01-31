<?php
namespace Concrete\Core\Cache\Page;

use Config;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page as ConcretePage;
use Loader;

/**
 * @deprecated Use ConcretePageCache
 */
class FilePageCache extends PageCache
{
    public function getRecord($mixed): ?PageCacheRecord
    {
        $file = $this->getCacheFile($mixed);
        if (file_exists($file)) {
            $contents = file_get_contents($file);
            $record = @unserialize($contents);
            if ($record instanceof PageCacheRecord) {
                return $record;
            }
        }
    }

    protected function getCacheFile($mixed)
    {
        $key = $this->getCacheKey($mixed);
        if ($key) {
            $key = hash('sha256', $key);
            $filename = $key . '.cache';
            $dir = Config::get('concrete.cache.page.directory') . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
            if ($dir && (!is_dir($dir))) {
                @mkdir($dir, Config::get('concrete.filesystem.permissions.directory'), true);
            }
            $path = $dir . '/' . $filename;

            return $path;
        }
    }

    public function purgeByRecord(\Concrete\Core\Cache\Page\PageCacheRecord $rec): void
    {
        $file = $this->getCacheFile($rec);
        if ($file && file_exists($file)) {
            @unlink($file);
        }
    }

    public function flush(): void
    {
        $fh = Loader::helper('file');
        $fh->removeAll(Config::get('concrete.cache.page.directory'));
    }

    public function purge(ConcretePage $c): void
    {
        $file = $this->getCacheFile($c);
        if ($file && file_exists($file)) {
            @unlink($file);
        }
    }

    public function set(ConcretePage $c, $content): void
    {
        $config = app(Repository::class);
        $dir = $config->get('concrete.cache.page.directory');
        if (!is_dir($dir)) {
            @mkdir($dir, $config->get('concrete.filesystem.permissions.directory'));
            @touch($dir . '/index.html');
        }
        $url = $c->getSite()->getSiteCanonicalURL();
        $lifetime = $c->getCollectionFullPageCachingLifetimeValue();
        $file = $this->getCacheFile($c);
        if ($file) {
            $response = new PageCacheRecord(
                $c,
                $content,
                $lifetime,
                $url,
                $this->getCacheKey($c),
                $this->getCacheHeaders($c),
            );
            if ($content) {
                file_put_contents($file, serialize($response));
            }
        }
    }
}
