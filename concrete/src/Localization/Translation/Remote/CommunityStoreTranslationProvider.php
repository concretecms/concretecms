<?php
namespace Concrete\Core\Localization\Translation\Remote;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Client\Client as HttpClient;
use DateTime;
use Exception;
use Gettext\Translations;
use Zend\Http\Request;
use Zend\Http\Response;

class CommunityStoreTranslationProvider implements ProviderInterface
{
    /**
     * @var string
     */
    const CORE_PACKAGE_HANDLE = 'concrete5';

    /**
     * The configuration repository containind the default values.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The cache to be used (won't be used if the cache lifetime is 0).
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The HTTP client to be used to communicate with the Community Translation server.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @param Repository $config The configuration repository containind the default values
     * @param Cache $cache The cache to be used (won't be used if the cache lifetime is 0)
     * @param HttpClient $httpClient The HTTP client to be used to communicate with the Community Translation server
     */
    public function __construct(Repository $config, Cache $cache, HttpClient $httpClient)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    /**
     * The API entry point.
     *
     * @var string|null
     */
    protected $entryPoint = null;

    /**
     * Set the API entry point.
     *
     * @param string $entryPoint
     *
     * @return $this
     */
    public function setEntryPoint($entryPoint)
    {
        $this->entryPoint = rtrim((string) $entryPoint, '/');

        return $this;
    }

    /**
     * Get the API entry point.
     *
     * @return string
     */
    public function getEntryPoint()
    {
        if ($this->entryPoint === null) {
            $this->setEntryPoint($this->config->get('concrete.i18n.community_translation.entry_point', ''));
        }

        return $this->entryPoint;
    }

    /**
     * The API token.
     *
     * @var string|null
     */
    protected $apiToken = null;

    /**
     * Set the API token.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setApiToken($value)
    {
        $this->apiToken = (string) $value;

        return $this;
    }

    /**
     * Get the API token.
     *
     * @return string
     */
    public function getApiToken()
    {
        if ($this->apiToken === null) {
            $this->apiToken = (string) $this->config->get('concrete.i18n.community_translation.api_token', '');
        }

        return $this->apiToken;
    }

    /**
     * The default progress limit.
     *
     * @var int|null
     */
    protected $progressLimit = null;

    /**
     * Set the default progress limit.
     *
     * @param int $defaultPogressLimit An integer between 0 (no translations at all) and 100 (all strings are translated)
     *
     * @return $this
     */
    public function setProgressLimit($value)
    {
        $this->progressLimit = min(max((int) $value, 0), 100);

        return $this;
    }

    /**
     * Get the default progress limit.
     *
     * @return int
     */
    public function getProgressLimit()
    {
        if ($this->progressLimit === null) {
            $this->setProgressLimit($this->config->get('concrete.i18n.community_translation.progress_limit', 90));
        }

        return $this->progressLimit;
    }

    /**
     * The cache life time (in seconds).
     *
     * @var int|null
     */
    protected $cacheLifetime = null;

    /**
     * Set the cache life time (in seconds).
     *
     * @param int $cacheLifetime if 0 (or less), the cache is disabled
     *
     * @return $this
     */
    public function setCacheLifetime($value)
    {
        $this->cacheLifetime = max((int) $value, 0);

        return $this;
    }

    /**
     * Get the cache life time (in seconds).
     *
     * @return int if 0, the cache is disabled
     */
    public function getCacheLifetime()
    {
        if ($this->cacheLifetime === null) {
            $this->setCacheLifetime($this->config->get('concrete.i18n.community_translation.cache_lifetime', 3600));
        }

        return $this->cacheLifetime;
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::getAvailableCoreStats()
     */
    public function getAvailableCoreStats($coreVersion, $progressLimit = null)
    {
        return $this->getAvailablePackageStats(static::CORE_PACKAGE_HANDLE, $coreVersion, $progressLimit);
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::getCoreStats()
     */
    public function getCoreStats($coreVersion, $localeID, $progressLimit = null)
    {
        return $this->getPackageStats(static::CORE_PACKAGE_HANDLE, $coreVersion, $localeID, $progressLimit);
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::getAvailablePackageStats()
     */
    public function getAvailablePackageStats($packageHandle, $packageVersion, $progressLimit = null)
    {
        $allLocales = [];
        if ($progressLimit === null) {
            $progressLimit = $this->getProgressLimit();
        }
        $data = null;
        $cacheLifetime = $this->getCacheLifetime();
        if ($cacheLifetime > 0 && $this->cache->isEnabled()) {
            $cacheItem = $this->cache->getItem('community_translation/' . $packageHandle . '@' . $packageVersion . 'L' . $progressLimit);
            /* @var \Stash\Item $cacheItem */
            if (!$cacheItem->isMiss()) {
                $data = $cacheItem->get();
            }
        } else {
            $cacheItem = null;
        }
        if ($data === null) {
            $request = $this->buildRequest('package/' . rawurlencode($packageHandle) . '/best-match-version/locales/' . $progressLimit . '/?v=' . rawurlencode($packageVersion));
            $this->httpClient->reset();
            $response = $this->httpClient->send($request);
            $this->httpClient->reset();
            if ($response->getStatusCode() === 404) {
                $data = [];
            } else {
                $data = $this->getJsonFromResponse($response);
            }
            if ($cacheItem !== null) {
                $cacheItem->set($data)->expiresAfter($cacheLifetime)->save();
            }
        }

        $result = [];
        if (!empty($data)) {
            foreach ($data['locales'] as $localeStats) {
                $result[$localeStats['id']] = new Stats(
                    $data['versionHandle'],
                    $localeStats['total'],
                    $localeStats['translated'],
                    empty($localeStats['translated']) ? null : new DateTime($localeStats['updated'])
                );
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::getPackageStats()
     */
    public function getPackageStats($packageHandle, $packageVersion, $localeID, $progressLimit = null)
    {
        $allStats = $this->getAvailablePackageStats($packageHandle, $packageVersion, $progressLimit);
        if (isset($allStats[$localeID])) {
            $result = $allStats[$localeID];
        } else {
            if (empty($allStats)) {
                $version = '';
                $total = 0;
            } else {
                $sampleStats = array_pop($allStats);
                $version = $sampleStats->getVersion();
                $total = $sampleStats->getTotal();
            }
            $result = new Stats($version, 0, 0);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::fetchCoreTranslations()
     */
    public function fetchCoreTranslations($coreVersion, $localeID, $formatHandle = 'mo')
    {
        return $this->fetchPackageTranslations(static::CORE_PACKAGE_HANDLE, $coreVersion, $localeID, $formatHandle);
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::fetchPackageTranslations()
     */
    public function fetchPackageTranslations($packageHandle, $packageVersion, $localeID, $formatHandle = 'mo')
    {
        $request = $this->buildRequest('package/' . rawurlencode($packageHandle) . '/best-match-version/translations/' . rawurldecode($localeID) . '/' . rawurldecode($formatHandle) . '/?v=' . rawurlencode($packageVersion));
        $this->httpClient->reset();
        $response = $this->httpClient->send($request);
        $this->httpClient->reset();
        $responseBody = $response->getBody();
        if ($response->getStatusCode() >= 400) {
            throw new Exception($responseBody);
        }

        return $responseBody;
    }

    /**
     * {@inheritdoc}
     *
     * @see ProviderInterface::fillTranslations()
     */
    public function fillTranslations(Translations $translations)
    {
        $request = $this->buildRequest('fill-translations/po/')->setMethod('POST');
        $files = $request->getFiles();
        $files->set(
            'f',
            [
                'formname' => 'file',
                'filename' => 'translations.po',
                'ctype' => 'application/octet-stream',
                'data' => $translations->toPoString(),
            ]
        );
        unset($translations);
        $this->httpClient->reset();
        $response = $this->httpClient->send($request);
        $this->httpClient->reset();
        $responseBody = $response->getBody();
        if ($response->getStatusCode() >= 400) {
            throw new Exception($responseBody);
        }
        $translations = Translations::fromPoString($responseBody);

        return $translations;
    }

    /**
     * @param string $path
     *
     * @return \Zend\Http\Request
     */
    protected function buildRequest($path)
    {
        $request = new Request();
        $apiToken = $this->getApiToken();
        if ($apiToken !== '') {
            $request->getHeaders()->addHeaders(['API-Token' => $apiToken]);
        }
        $path = (string) $path;
        if ($path !== '' && $path[0] !== '/') {
            $path = '/' . $path;
        }
        $request->setUri($this->getEntryPoint() . $path);

        return $request;
    }

    /**
     * @param Response $response
     *
     * @throws Exception
     *
     * @return array
     */
    protected function getJsonFromResponse(Response $response)
    {
        $responseBody = $response->getBody();
        if ($response->getStatusCode() >= 400) {
            throw new Exception($responseBody);
        }
        $json = @json_decode($responseBody, true);
        if ($json === null) {
            throw new Exception('Failed to decode CommunityTranslation response');
        }

        return $json;
    }
}
