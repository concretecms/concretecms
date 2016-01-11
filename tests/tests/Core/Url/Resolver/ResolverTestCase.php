<?php

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

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $url = \Concrete\Core\Url\UrlImmutable::createFromUrl(\Core::make('url/canonical'));
        $this->canonicalUrl = $url;
    }

    public function tearDown()
    {
        \Core::forgetInstance('url/canonical');
    }

    protected function canonicalUrlWithPath($path, $dispatcher = null)
    {
        if (is_null($dispatcher)) {
            $rewriting = \Config::get('concrete.seo.url_rewriting');
            $rewrite_all = \Config::get('concrete.seo.url_rewriting_all');
            $in_dashboard = \Core::make('helper/concrete/dashboard')->inDashboard($path);

            // If rewriting is disabled, or all_rewriting is disabled and we're
            // in the dashboard, add the dispatcher.
            $dispatcher = (!$rewriting || (!$rewrite_all && $in_dashboard));
        }

        if ($dispatcher) {
            $path = new \Concrete\Core\Url\Components\Path($path);
            $path->prepend(DISPATCHER_FILENAME);
        }
        $canonical_path = $this->canonicalUrl->getPath();
        $canonical_path->append($path);

        return $this->canonicalUrl->setPath($canonical_path);
    }
}
