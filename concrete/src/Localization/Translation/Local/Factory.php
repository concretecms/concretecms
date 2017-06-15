<?php
namespace Concrete\Core\Localization\Translation\Local;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Package\Package;
use DateTime;
use Exception;
use Gettext\Translations;
use Illuminate\Filesystem\Filesystem;
use Throwable;

class Factory implements FactoryInterface
{
    /**
     * @var string
     */
    const CACHE_PREFIX = 'local_translation_files';

    /**
     * @var int
     */
    const CACHE_DURATION = 1800;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @param Cache $cache
     * @param Filesystem $fs
     */
    public function __construct(Cache $cache, Filesystem $fs)
    {
        $this->cache = $cache;
        $this->fs = $fs;
    }

    /**
     * {@inheritdoc}
     *
     * @see FactoryInterface::getAvailableCoreStats()
     */
    public function getAvailableCoreStats()
    {
        $result = [];
        if ($this->fs->isDirectory(DIR_LANGUAGES)) {
            foreach ($this->fs->directories(DIR_LANGUAGES) as $localeDirectory) {
                $localeID = basename($localeDirectory);
                if (preg_match('/^[a-z]{2,3}($|_)/', $localeID)) {
                    $mo = DIR_LANGUAGES . '/' . $localeID . '/LC_MESSAGES/messages.mo';
                    if ($this->fs->isFile($mo)) {
                        $result[$localeID] = $this->getMoFileStats($mo);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see FactoryInterface::getCoreStats()
     */
    public function getCoreStats($localeID)
    {
        return $this->getMoFileStats(DIR_LANGUAGES . '/' . $localeID . '/LC_MESSAGES/messages.mo');
    }

    /**
     * {@inheritdoc}
     *
     * @see FactoryInterface::getAvailablePackageStats()
     */
    public function getAvailablePackageStats(Package $package)
    {
        $result = [];
        $languagesDir = $package->getPackagePath() . '/' . DIRNAME_LANGUAGES;
        if ($this->fs->isDirectory($languagesDir)) {
            foreach ($this->fs->directories($languagesDir) as $localeDirectory) {
                $localeID = basename($localeDirectory);
                if (preg_match('/^[a-z]{2,3}($|_)/', $localeID)) {
                    $mo = $languagesDir . '/' . $localeID . '/LC_MESSAGES/messages.mo';
                    if ($this->fs->isFile($mo)) {
                        $result[$localeID] = $this->getMoFileStats($mo);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see FactoryInterface::getPackageStats()
     */
    public function getPackageStats(Package $package, $localeID)
    {
        return $this->getMoFileStats($package->getPackagePath() . '/' . DIRNAME_LANGUAGES . '/' . $localeID . '/LC_MESSAGES/messages.mo');
    }

    /**
     * {@inheritdoc}
     *
     * @see FactoryInterface::clearCache()
     */
    public function clearCache()
    {
        $this->cache->getItem(self::CACHE_PREFIX)->clear();
    }

    /**
     * Get stats for a \Gettext\Translations instance.
     *
     * @param Translations $translations
     * @param DateTime $defaultUpdatedOn
     *
     * @return null|array {
     *     @var string $version
     *     @var DateTime $updatedOn
     * }
     */
    protected function getTranslationsStats(Translations $translations, DateTime $defaultUpdatedOn)
    {
        $result = null;
        foreach ($translations as $translation) {
            if ($translation->hasTranslation()) {
                $result = [
                    'version' => '',
                    'updatedOn' => $defaultUpdatedOn,
                ];
                break;
            }
        }
        if ($result !== null) {
            $h = $translations->getHeader('Project-Id-Version');
            if ($h && preg_match('/\s([\S]*\d[\S]*)$/', $h, $m)) {
                $result['version'] = $m[1];
            }
            $h = $translations->getHeader('PO-Revision-Date');
            if ($h) {
                try {
                    $result['updatedOn'] = new DateTime($h);
                } catch (Exception $x) {
                } catch (Throwable $x) {
                }
            }
        }

        return $result;
    }

    /**
     * Get stats for a gettext .mo file.
     *
     * @param string $moFile The full path to a gettext .mo file (it may not exist)
     *
     * @return Stats
     */
    protected function getMoFileStats($moFile)
    {
        if ($this->fs->isFile($moFile)) {
            $lastModifiedTimestamp = $this->fs->lastModified($moFile);
            if ($this->cache->isEnabled()) {
                $cacheItem = $this->cache->getItem(self::CACHE_PREFIX . '/' . md5($moFile) . '_' . $lastModifiedTimestamp);
            } else {
                $cacheItem = null;
            }
            if ($cacheItem === null || $cacheItem->isMiss()) {
                $stats = $this->getTranslationsStats(Translations::fromMoFile($moFile), new DateTime('@' . $lastModifiedTimestamp));
                if ($cacheItem !== null) {
                    $cacheItem->set($stats)->expiresAfter(self::CACHE_DURATION)->save();
                }
            } else {
                $stats = $cacheItem->get();
            }
        } else {
            $stats = null;
        }

        return new Stats('mo', $moFile, ($stats === null) ? '' : $stats['version'], ($stats === null) ? null : $stats['updatedOn']);
    }
}
