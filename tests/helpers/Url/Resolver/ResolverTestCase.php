<?php

namespace Concrete\TestHelpers\Url\Resolver;

use PHPUnit_Framework_TestCase;

abstract class ResolverTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Url\UrlImmutable
     */
    protected $canonicalUrl;

    /**
     * @var \Concrete\Core\Url\Resolver\UrlResolverInterface
     */
    protected $urlResolver;

    protected function setUp()
    {
        $url = \Concrete\Core\Url\UrlImmutable::createFromUrl(\Core::make('url/canonical'));
        $this->canonicalUrl = $url;
    }

    public function tearDown()
    {
        \Core::forgetInstance('url/canonical');
    }

    protected function canonicalUrlWithPath($path, $dispatcher = null)
    {
        if ($dispatcher === null) {
            $site = \Core::make('site');
            $siteConfig = $site->getSite()->getConfigRepository();
            $rewriting = $siteConfig->get('seo.url_rewriting');
            $rewrite_all = $siteConfig->get('seo.url_rewriting_all');

            $in_dashboard = \Core::make('helper/concrete/dashboard')->inDashboard($path);

            // If rewriting is disabled, or all_rewriting is disabled and we're
            // in the dashboard, add the dispatcher.
            $dispatcher = (!$rewriting || (!$rewrite_all && $in_dashboard));
        }

        $globalConfig = \Core::make('config');
        $trailing_slash = (bool) $globalConfig->get('concrete.seo.trailing_slash');

        $path = new \Concrete\Core\Url\Components\Path($path, $trailing_slash);
        if ($dispatcher) {
            $path->prepend(DISPATCHER_FILENAME);
        }

        $url = \Concrete\Core\Url\Url::createFromUrl($this->canonicalUrl);
        $path->prepend($this->canonicalUrl->getPath());

        return $url->setPath($path);
    }
}
