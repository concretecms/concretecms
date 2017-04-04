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
        return $this->getMoFileStats($package->getPackagePath() . DIRNAME_LANGUAGES . '/' . $localeID . '/LC_MESSAGES/messages.mo');
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
     *
     * @return First array item is the number of translated strings, second array item is the date/time of the last update (if available)
     */
    protected function getTranslationsStats(Translations $translations)
    {
        $translated = 0;
        foreach ($translations as $translation) {
            if ($translation->hasTranslation()) {
                ++$translated;
            }
        }
        $lastUpdated = null;
        $dt = ($translated > 0) ? $translations->getHeader('PO-Revision-Date') : false;
        unset($translations);
        if ($dt) {
            try {
                $lastUpdated = new DateTime($dt);
            } catch (Exception $x) {
            } catch (Throwable $x) {
            }
        }

        return [
            $translated,
            $lastUpdated,
        ];
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
            $cache = $this->cache->getItem(self::CACHE_PREFIX . '/' . md5($mo) . '_' . $lastModifiedTimestamp);
            if ($cache->isMiss()) {
                list($translated, $lastUpdated) = $this->getTranslationsStats(Translations::fromMoFile($moFile));
                if ($translated > 0 && $lastUpdated === null) {
                    $lastUpdated = new DateTime('@' . $lastModifiedTimestamp);
                }
                $data = [
                    'translated' => $translated,
                    'lastUpdated' => $lastUpdated,
                ];
                $cache->set($data)->expiresAfter(self::CACHE_DURATION)->save();
            } else {
                $data = $cache->get();
                $translated = $data['translated'];
                $lastUpdated = $data['lastUpdated'];
            }
        } else {
            $translated = 0;
            $lastUpdated = null;
        }

        return new Stats('mo', $moFile, $translated, $lastUpdated);
    }
}
