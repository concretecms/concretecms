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
    /** @var Request */
    protected $request;

    /** @var Application */
    protected $app;

    /** @var Url */
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
     * Resolve url's from any type of input.
     *
     * This method MUST either return a `\League\URL\URL` when a url is resolved
     * or null when a url cannot be resolved.
     *
     * If the first argument provided is a page object, we will use that object to determine the site tree
     * (and thus the canonical url) to use.
     *
     * @param array $arguments A list of the arguments
     * @param \League\URL\URLInterface $resolved
     *
     * @return \League\URL\URLInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        $config = null;
        $page = null;

        // Canonical urls for pages can be different than for the entire site
        if (count($arguments) && head($arguments) instanceof Page) {
            /** @var Page $page */
            $page = head($arguments);
            $tree = $page->getSiteTreeObject();

            if ($tree instanceof SiteTree && $site = $tree->getSite()) {
                $config = $site->getConfigRepository();
            }

        } elseif ($this->cached) {
            return $this->cached;
        }

        // Get the config from the current site tree
        if ($config === null && $this->app->isInstalled()) {
            $site = $this->app['site']->getSite();
            if (is_object($site)) {
                $config = $site->getConfigRepository();
            }
        }

        // Determine trailing slash setting
        $trailing_slashes = $config && $config->get('seo.trailing_slash') ? Url::TRAILING_SLASHES_ENABLED : Url::TRAILING_SLASHES_DISABLED;

        $url = UrlImmutable::createFromUrl('', $trailing_slashes);

        $url = $url->setHost(null);
        $url = $url->setScheme(null);

        if ($config && $configUrl = $site->getSiteCanonicalURL()) {
            $canonical = UrlImmutable::createFromUrl($configUrl, $trailing_slashes);

            if ($configSslUrl = $config->get('seo.canonical_ssl_url')) {
                $canonical_ssl = UrlImmutable::createFromUrl($configSslUrl, $trailing_slashes);
            }

            $url = $url->setHost($canonical->getHost());
            $url = $url->setScheme($canonical->getScheme());

            // If the request is over https
            if (strtolower($this->request->getScheme()) == 'https') {
                // If the canonical ssl url is set, respect the canonical ssl url.
                if (isset($canonical_ssl)) {
                    $url = $url->setHost($canonical_ssl->getHost());
                    $url = $url->setScheme($canonical_ssl->getScheme());
                    if (intval($canonical_ssl->getPort()->get()) > 0) {
                        $url = $url->setPort($canonical_ssl->getPort());
                    }
                } else {
                    // If the canonical url is http, lets just say https for the canonical url.
                    if (strtolower($canonical->getScheme()) == 'http') {
                        $url = $url->setScheme('https');
                    }
                    if (intval($canonical->getPort()->get()) > 0) {
                        $url = $url->setPort($canonical->getPort());
                    }
                }
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
