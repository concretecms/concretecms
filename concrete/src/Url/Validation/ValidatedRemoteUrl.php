<?php

namespace Concrete\Core\Url\Validation;

use Concrete\Core\Url\Url;

class ValidatedRemoteUrl
{
    /**
     * @var \Concrete\Core\Url\Url
     */
    protected $url;

    /**
     * @var string
     */
    protected $ip;

    public function __construct(Url $url, string $ip)
    {
        $this->url = $url;
        $this->ip = $ip;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getScheme(): string
    {
        return strtolower((string) $this->url->getScheme());
    }

    public function getHost(): string
    {
        return trim((string) $this->url->getHost());
    }

    public function getPort(): int
    {
        $port = $this->url->getPort();
        $port = $port ? $port->get() : null;

        return $port ? (int) $port : ($this->getScheme() === 'http' ? 80 : 443);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function __toString(): string
    {
        return (string) $this->url;
    }
}
