<?php

namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Config;
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
     * @param array $arguments A list of the arguments
     * @param \League\URL\URLInterface $resolved
     *
     * @return \League\URL\URLInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if ($this->cached) {
            return $this->cached;
        }

        $config = $this->app['config'];

        // Determine trailing slash setting
        $trailing_slashes = $config->get('concrete.seo.trailing_slash') ? Url::TRAILING_SLASHES_ENABLED : Url::TRAILING_SLASHES_DISABLED;

        $url = Url::createFromUrl('', $trailing_slashes);

        $url->setHost(null);
        $url->setScheme(null);

        if ($config->get('concrete.seo.canonical_url')) {
            $canonical = UrlImmutable::createFromUrl($config->get('concrete.seo.canonical_url'), $trailing_slashes);

            // If the request is over https and the canonical url is http, lets just say https for the canonical url.
            if (strtolower($canonical->getScheme()) == 'http' && strtolower($this->request->getScheme()) == 'https') {
                $url->setScheme('https');
            } else {
                $url->setScheme($canonical->getScheme());
            }

            $url->setHost($canonical->getHost());

            if (intval($canonical->getPort()->get()) > 0) {
                $url->setPort($canonical->getPort());
            }
        } else {
            $host = $this->request->getHost();
            $scheme = $this->request->getScheme();
            if ($scheme && $host) {
                $url->setScheme($scheme)
                    ->setHost($host)
                    ->setPort($this->request->getPort());
            }
        }

        if ($relative_path = $this->app['app_relative_path']) {
            $url = $url->setPath($relative_path);
        }

        $this->cached = UrlImmutable::createFromUrl($url, $trailing_slashes);

        return $this->cached;
    }

    /**
     * Clear the cached canonical URL
     */
    public function clearCached()
    {
        $this->cached = null;
    }

}
