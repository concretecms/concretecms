<?php

namespace Concrete\Core\User;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Concrete\Core\Url\UrlImmutable;
use Throwable;

class PostLocationLocationUrl
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Url\Resolver\CanonicalUrlResolver
     */
    protected $canonicalUrlResolver;

    public function __construct(Repository $config, CanonicalUrlResolver $canonicalUrlResolver)
    {
        $this->config = $config;
        $this->canonicalUrlResolver = $canonicalUrlResolver;
    }

    /**
     * Return a redirect URL if it's allowed, or an empty string otherwise.
     *
     * @param string $url
     *
     * @return string
     */
    public function getAllowedRedirectUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        $redirectUrl = $this->createAbsoluteUrl($url);
        if ($redirectUrl === null) {
            return '';
        }

        foreach ($this->getAllowedRedirectUrlBases() as $allowedBase) {
            $allowedUrl = $this->createAbsoluteUrl($allowedBase);
            if ($allowedUrl === null) {
                continue;
            }
            if (
                strtolower((string) $allowedUrl->getScheme()) !== strtolower((string) $redirectUrl->getScheme()) ||
                strtolower((string) $allowedUrl->getHost()) !== strtolower((string) $redirectUrl->getHost()) ||
                $this->getUrlPort($allowedUrl) !== $this->getUrlPort($redirectUrl)
            ) {
                continue;
            }
            if ($this->matchesAllowedPath((string) $allowedUrl->getPath(), (string) $redirectUrl->getPath())) {
                return $url;
            }
        }

        return '';
    }

    /**
     * @return string[]
     */
    public function getAllowedRedirectUrlBases()
    {
        $configured = $this->config->get('concrete.security.post_login_redirect_url_allowlist', []);
        if (is_array($configured) && $configured !== []) {
            return array_values(array_filter(array_map('trim', $configured), 'strlen'));
        }

        $canonicalUrl = (string) $this->canonicalUrlResolver->resolve([]);
        if ($canonicalUrl !== '') {
            return [$canonicalUrl];
        }

        return [];
    }

    /**
     * @param string $url
     *
     * @return \Concrete\Core\Url\UrlImmutable|null
     */
    protected function createAbsoluteUrl($url)
    {
        try {
            $url = UrlImmutable::createFromUrl(trim((string) $url));
        } catch (Throwable $x) {
            return null;
        }

        if ((string) $url->getScheme() === '' || (string) $url->getHost() === '') {
            return null;
        }

        return $url;
    }

    /**
     * @param \Concrete\Core\Url\UrlImmutable $url
     *
     * @return int
     */
    protected function getUrlPort(UrlImmutable $url)
    {
        $port = (int) $url->getPort()->get();
        if ($port > 0) {
            return $port;
        }

        switch (strtolower((string) $url->getScheme())) {
            case 'http':
                return 80;
            case 'https':
                return 443;
            default:
                return 0;
        }
    }

    /**
     * @param string $allowedPath
     * @param string $redirectPath
     *
     * @return bool
     */
    protected function matchesAllowedPath($allowedPath, $redirectPath)
    {
        $allowedPath = $this->normalizePathForComparison($allowedPath);
        $redirectPath = $this->normalizePathForComparison($redirectPath);
        if ($allowedPath === '/') {
            return true;
        }

        return $redirectPath === $allowedPath || strpos($redirectPath, $allowedPath . '/') === 0;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function normalizePathForComparison($path)
    {
        $path = '/' . ltrim((string) $path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
