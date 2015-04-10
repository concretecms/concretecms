<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Url;
use Concrete\Core\Url\UrlImmutable;

class CanonicalUrlResolver implements UrlResolverInterface
{

    /**
     * Resolve url's from any type of input
     *
     * This method MUST either return a `\League\URL\URL` when a url is resolved
     * or null when a url cannot be resolved.
     *
     * @param array                    $arguments A list of the arguments
     * @param \League\URL\URLInterface $resolved
     * @return \League\URL\URLInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        $url = Url::createFromUrl('', !!\Config::get('concrete.seo.trailing_slash'));

        // Normalize
        $url->setHost(null);
        $url->setScheme(null);
        $url->setPort(null);

        if (\Config::get('concrete.seo.canonical_host')) {
            $url->getHost()->set(\Config::get('concrete.seo.canonical_host'));
        } else {
            $url->getHost()->set(\Request::getInstance()->getHost());
        }

        if ($url->getHost()->get() && !$url->getScheme()->get()) {
            $url->setScheme(\Request::getInstance()->getScheme());
        }

        $port = null;
        if ($config_port = \Config::get('concrete.seo.canonical_port')) {
            $port = intval($config_port, 10);
        } else {
            $port = intval(\Request::getInstance()->getPort(), 10);
        }

        if ($relative_path = \Core::getApplicationRelativePath()) {
            $url = $url->setPath($relative_path);
        }

        $scheme = strtolower($url->getScheme());
        if ($port && $scheme == 'http' || $scheme == 'https') {
            if (($scheme == 'http' && $port != 80) ||
                ($scheme == 'https' && $port != 443)
            ) {
                $url->setPort($port);
            }
        } elseif ($port) {
            $url->setPort($port);
        }

        return UrlImmutable::createFromUrl($url);
    }

}
