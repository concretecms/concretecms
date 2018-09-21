<?php

namespace Concrete\Core\Cache;

use Concrete\Core\Application\Application;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Foundation\Environment;
use Concrete\Core\Localization\Localization;
use Exception;
use FilesystemIterator;
use Illuminate\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CacheClearer
{

    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    private $dispatcher;

    /** @var \Concrete\Core\Application\Application */
    private $application;

    /** @var \Concrete\Core\Database\DatabaseManager */
    private $manager;

    /** @var \Concrete\Core\Config\Repository\Repository */
    private $repository;

    /** @var \Illuminate\Filesystem\Filesystem */
    private $filesystem;

    /** @var LoggerInterface */
    private $logger;

    /** @var \Concrete\Core\Cache\Level\ObjectCache */
    private $caches = [
        'cache' => 'cache',
        'expensive' => 'cache/expensive',
        'overrides' => 'cache/overrides',
        'request' => 'cache/request',
        'page' => 'cache/page'
    ];

    /**
     * @var bool
     */
    private $clearGlobalAreas = true;

    public function __construct(
        EventDispatcher $dispatcher,
        DatabaseManager $manager,
        Repository $repository,
        Application $application,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->dispatcher = $dispatcher;
        $this->application = $application;
        $this->manager = $manager;
        $this->repository = $repository;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    public function flush()
    {
        $this->dispatcher->dispatch('on_cache_flush');

        $this->logger->notice(t('Clearing cache with CacheClearer::flush().'));

        // Flush the cache objects
        $this->flushCaches();

        // Clear the /files/cache directory
        $directory = $this->repository->get('concrete.cache.directory');
        $this->clearCacheDirectory($directory);

        // Clear the file thumbnail path cache
        $this->clearDatabaseCache();

        // clear the environment overrides cache (This is probably already cleared)
        $this->application->make(Environment::class)->clearOverrideCache();

        // Clear localization cache
        $this->clearLocalizationCache();

        // clear block type cache
        $this->clearBlockTypeCache();

        // Clear precompiled script bytecode caches
        $this->clearOpcodeCache();

        // Setup the filesystem
        $this->setupFilesystem();

        // Delete global areas without any blocks
        $this->deleteEmptyGlobalAreas();

        $this->dispatcher->dispatch('on_cache_flush_end');
    }

    /**
     * @param boolean $clearGlobalAreas
     */
    public function setClearGlobalAreas($clearGlobalAreas)
    {
        $this->clearGlobalAreas = $clearGlobalAreas;
    }

    /**
     * Set a cache object to be cleared
     *
     * @param $handle
     * @param $cache
     */
    public function setCache($handle, $cache)
    {
        $this->caches[$handle] = $cache;
    }

    /**
     * A generator that populates the cache objects from the container
     * @return \Generator|FlushableInterface[]
     */
    protected function getCaches()
    {
        foreach ($this->caches as $key => $cache) {
            if (!$cache instanceof FlushableInterface) {
                $cache = $this->application->make($cache);
            }

            if ($cache instanceof FlushableInterface) {
                yield $key => $cache;
            }
        }
    }

    /**
     * Flush all the caches
     */
    protected function flushCaches()
    {
        foreach ($this->getCaches() as $key => $cache) {
            $cache->flush();
        }
    }

    /**
     * Clear out items from the cache directory
     * @param $directory
     */
    protected function clearCacheDirectory($directory)
    {
        foreach ($this->filesToClear($directory) as $file) {
            if ($file->isDir()) {
                $this->filesystem->deleteDirectory($file->getPathname());
            } else {
                $this->filesystem->delete($file->getPathname());
            }
        }
    }

    /**
     * @param $directory
     * @return \Generator|\SplFileInfo[]
     */
    protected function filesToClear($directory)
    {
        $directory = str_replace('/', DIRECTORY_SEPARATOR, $directory);
        try {
            $iterator = new FilesystemIterator($directory);
        } catch (\UnexpectedValueException $e) {
            // The directory doesn't exist
            return;
        }

        $exclude = [];

        if (!$this->repository->get('concrete.cache.clear.thumbnails', true)) {
            $exclude[] = $directory . DIRECTORY_SEPARATOR . 'thumbnails';
        } else {
            $this->logger->notice(t('Clearing cache thumbnails directory.'));
        }

        /** @var \SplFileInfo $item */
        foreach ($iterator as $item) {
            if (!in_array($item->getPathname(), $exclude, true)) {
                yield $item;
            }
        }
    }

    private function setupFilesystem()
    {
        $directory = $this->repository->get('concrete.cache.directory');
        $perms = $this->repository->get('concrete.filesystem.permissions');
        $directoryPermissions = array_get($perms, 'directory');
        $filePermissions = array_get($perms, 'file');

        $create = [
            $directory,
            $directory . '/thumbnails'
        ];

        foreach ($create as $dir) {
            if (is_dir($dir) || $this->filesystem->makeDirectory($dir, $directoryPermissions)) {
                if (!$this->filesystem->exists($dir . '/index.html')) {
                    @touch($dir . '/index.html', $filePermissions);
                }
            }
        }
    }

    private function clearDatabaseCache()
    {
        $connection = $this->manager->connection();
        $sql = $connection->getDatabasePlatform()->getTruncateTableSQL('FileImageThumbnailPaths');
        try {
            $connection->executeUpdate($sql);
        } catch (Exception $e) {
        }
    }

    protected function clearLocalizationCache()
    {
        Localization::clearCache();
    }

    protected function clearBlockTypeCache()
    {
        BlockType::clearCache();
    }

    protected function clearOpcodeCache()
    {
        OpCache::clear();
    }

    protected function deleteEmptyGlobalAreas()
    {
        if ($this->clearGlobalAreas) {
            GlobalArea::deleteEmptyAreas();
        }
    }
}
