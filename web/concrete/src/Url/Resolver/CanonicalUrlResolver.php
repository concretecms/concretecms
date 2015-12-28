<?php

namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Application\Application;
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
    * @param array                    $arguments A list of the arguments
    * @param \League\URL\URLInterface $resolved
    *
    * @return \League\URL\URLInterface
    */
   public function resolve(array $arguments, $resolved = null)
   {
       $url = Url::createFromUrl('');

       $url->setHost(null);
       $url->setScheme(null);


       if (\Config::get('concrete.seo.canonical_url')) {
           $canonical = UrlImmutable::createFromUrl(Config::get('concrete.seo.canonical_url'));

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
                   ->setPortIfNecessary(Request::getInstance()->getPort());
           }
       }

       if ($relative_path = \Core::getApplicationRelativePath()) {
           $url = $url->setPath($relative_path);
       }

       return UrlImmutable::createFromUrl($url);
   }
}
