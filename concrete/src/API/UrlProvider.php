<?php
namespace Concrete\Core\API;

use Concrete\Core\Config\Repository\Repository;

class UrlProvider implements APIProviderInterface
{

    protected $url;
    protected $config;

    public function __construct($url, $config = [])
    {
        $this->url = $url;
        $this->config = $config;
    }

    public function getBaseUrl()
    {
        return $this->url;
    }

    public function getConfig()
    {
        return $this->config;
    }

}