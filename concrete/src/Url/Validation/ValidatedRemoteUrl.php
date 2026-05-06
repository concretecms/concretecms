<?php

namespace Concrete\Core\Url\Validation;

class ValidatedRemoteUrl
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $ip;

    public function __construct(string $url, string $scheme, string $host, int $port, string $ip)
    {
        $this->url = $url;
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->ip = $ip;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getIp(): string
    {
        return $this->ip;
    }
}
