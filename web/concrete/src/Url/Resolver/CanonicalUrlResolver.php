<?php

namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Url;
use Concrete\Core\Url\UrlImmutable;
use Request;

class CanonicalUrlResolver implements UrlResolverInterface
{
   /**
    * Resolve url's from any type of input.
    *
    * This method MUST either return a `\League\URL\URL` when a url is resolved
    * or null when a url cannot be resolved.
    *
    * @param array                    $arguments A list of the arguments
    * @param \League\URL\URLInterface $resolved
    *
    * @return \League\URL\URLInterface
    */
   public function resolve(array $arguments, $resolved = null)
   {
       $url = Url::createFromUrl('', (bool) \Config::get('concrete.seo.trailing_slash'));

       $url->setHost(null);
       $url->setScheme(null);

       if (\Config::get('concrete.seo.canonical_url')) {
           $canonical = UrlImmutable::createFromUrl(\Config::get('concrete.seo.canonical_url'),
                (bool) \Config::get('concrete.seo.trailing_slash')
            );
           $url->getHost()->set($canonical->getHost());
           $url->getScheme()->set($canonical->getScheme());
           if (intval($canonical->getPort()->get()) > 0) {
               $url->getPort()->set($canonical->getPort());
           }
       } else {
           $scheme = Request::getInstance()->getScheme();
           $host = Request::getInstance()->getHost();
           if ($scheme && $host) {
               $url
                   ->setScheme($scheme)
                   ->setHost($host)
                   ->setPortIfNecessary(Request::getInstance()->getPort());
           }
       }

       if ($relative_path = \Core::getApplicationRelativePath()) {
           $url = $url->setPath($relative_path);
       }

       return UrlImmutable::createFromUrl($url);
   }
}
