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
     * @param \League\Url\UrlInterface $resolved
     * @return \League\Url\UrlInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        $url = Url::createFromUrl('', !!\Config::get('concrete.seo.trailing_slash'));

        // Normalize
        $url->setScheme(null);
        $url->setHost(null);
        $url->setPort(null);

        if (\Config::get('concrete.seo.force_ssl')) {
            $scheme = 'https';
        } else {
            $scheme = \Request::getInstance()->getScheme();
        }
        $host = \Config::get('concrete.seo.canonical_host');
        if (!$host) {
            $host = \Request::getInstance()->getHost();
        }
        if ($scheme && $host) {
            $url->setScheme($scheme)->setHost($host);
            $port = \Config::get('concrete.seo.canonical_port');
            if (!$port) {
                $port = \Request::getInstance()->getPort();
            }
            if ($port) {
                switch ("$scheme:$port") {
                    case 'http:80':
                    case 'https:443':
                        break;
                    default:
                        $url->setPort($port);
                        break;
                }
            }
        }

        return UrlImmutable::createFromUrl($url);
    }

}
