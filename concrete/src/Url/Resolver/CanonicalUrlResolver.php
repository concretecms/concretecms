<?php

namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Url;
use Concrete\Core\Url\UrlImmutable;

class CanonicalUrlResolver implements UrlResolverInterface
{
    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Url\Url
     */
    protected $cached;

    /**
     * CanonicalUrlResolver constructor.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\UrlResolverInterface::resolve()
     */
    public function resolve(array $arguments, $resolved = null)
    {
        $page = null;
        $site = null;
        // Canonical urls for pages can be different than for the entire site
        if (isset($arguments[0]) && $arguments[0] instanceof Page) {
            $page = head($arguments);
            $tree = $page->getSiteTreeObject();
            if ($tree instanceof SiteTree) {
                $site = $tree->getSite();
            }
        } elseif ($this->cached) {
            return $this->cached;
        }
        /* @var \Concrete\Core\Page\Page|null $page */

        // Get the site from the current site tree
        if ($site === null && $this->app->isInstalled()) {
            $site = $this->app->make('site')->getSite();
        }
        /* @var \Concrete\Core\Entity\Site\Site|null $site */

        // Determine trailing slash setting
        $trailing_slashes = $this->app->make('config')->get('concrete.seo.trailing_slash') ? Url::TRAILING_SLASHES_ENABLED : Url::TRAILING_SLASHES_DISABLED;

        $url = UrlImmutable::createFromUrl('', $trailing_slashes);

        $url = $url->setHost(null);
        $url = $url->setScheme(null);

        if ($site && $configUrl = $site->getSiteCanonicalURL()) {
            $requestScheme = strtolower($this->request->getScheme());

            $canonical = UrlImmutable::createFromUrl($configUrl, $trailing_slashes);

            $canonicalToUse = $canonical;

            if ($configUrlAlternative = $site->getSiteAlternativeCanonicalURL()) {
                $canonical_alternative = UrlImmutable::createFromUrl($configUrlAlternative, $trailing_slashes);
                if (
                    strtolower($canonical->getScheme()) !== $requestScheme &&
                    strtolower($canonical_alternative->getScheme()) === $requestScheme
                ) {
                    $canonicalToUse = $canonical_alternative;
                }
            }

            $url = $url->setScheme($canonicalToUse->getScheme());
            $url = $url->setHost($canonicalToUse->getHost());
            if ((int) $canonicalToUse->getPort()->get() > 0) {
                $url = $url->setPort($canonicalToUse->getPort());
            }
        } else {
            // This fallthrough is dangerous. Make sure that you define your canonical URL so that we don't have to guess!
            $host = $this->request->getHost();
            $scheme = $this->request->getScheme();
            if ($scheme && $host) {
                $url = $url->setScheme($scheme)
                    ->setHost($host)
                    ->setPort($this->request->getPort());
            }
        }

        if ($relative_path = $this->app['app_relative_path']) {
            $url = $url->setPath($relative_path);
        }

        // Don't cache page specific canonical urls
        if (!$page) {
            $this->cached = $url;
        }

        return $url;
    }

    /**
     * Clear the cached canonical URL.
     */
    public function clearCached()
    {
        $this->cached = null;
    }
}
