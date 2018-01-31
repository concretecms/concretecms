<?php
namespace Concrete\Core\API;

use Concrete\Core\Config\Repository\Repository;
use GuzzleHttp\Command\Guzzle\Description;

final class APIFactory
{

    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
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
        $description = $this->config->get('api::' . $name);
        $baseUrl = $this->config->get('app.api.base_url') . '/' .
            $name . '/';
        $description['baseUrl'] = $baseUrl;
        return $description;
    }





}