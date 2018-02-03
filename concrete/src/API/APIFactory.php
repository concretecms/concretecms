<?php
namespace Concrete\Core\API;

use Concrete\Core\Config\Repository\Repository;
use GuzzleHttp\Command\Guzzle\Description;

final class APIFactory
{

    /**
     * @var Repository
     */
    protected $config;


    protected $additionalMethodGroups = [];

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function registerMethodGroup($group, $pkgHandle)
    {
        $this->additionalMethodGroups[$group] = $pkgHandle;
    }

    public function create(APIProviderInterface $provider, $config = [])
    {
        $uri = trim($provider->getBaseUrl(), '/') . '/index.php';
        $config = array_merge($config, $provider->getConfig());
        $api = new API($this, $uri, $config);
        return $api;
    }

    public function getDescriptionConfig($name)
    {
        if (array_key_exists($name, $this->additionalMethodGroups)) {
            $key = sprintf('%s::%s/%s', $this->additionalMethodGroups[$name], 'api', $name);
        } else {
            $key = sprintf('api::%s', $name);
        }
        $description = $this->config->get($key);
        $baseUrl = $this->config->get('app.api.base_url') . '/' . $name . '/';
        $description['baseUrl'] = $baseUrl;
        return $description;
    }





}