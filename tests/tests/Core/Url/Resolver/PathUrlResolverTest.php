<?php

require_once __DIR__ . "/ResolverTestCase.php";

class PathUrlResolverTest extends ResolverTestCase
{

    protected function setUp()
    {
        $this->urlResolver = new \Concrete\Core\Url\Resolver\PathUrlResolver();
    }

    public function testResolveSinglePath()
    {
        $path = '/some/path/to/something';

        $url = $this->canonicalUrlWithPath($path);
        $this->assertEquals($this->urlResolver->resolve(array($path)), (string) $url);
    }

    public function testSkipResolved()
    {
        $path = '/some/path/to/something';

        $resolved = uniqid();
        $this->assertEquals(
            $resolved,
            $this->urlResolver->resolve(array($path), $resolved));
    }

    public function testBadArguments()
    {
        $this->assertEquals(
            null,
            $this->urlResolver->resolve(array()));
    }

    public function testObjectPath()
    {
        $path = '/some/path/to/something';

        $this->assertEquals(
            $this->canonicalUrlWithPath($path),
            $this->urlResolver->resolve(
                array(new \Concrete\Core\Url\Components\Path($path))));
    }

    public function testUrlWithQuery()
    {
        $this->assertEquals(
            null,
            $this->urlResolver->resolve(array()));
    }

    public function testSegmentedPath()
    {
        $path = "/some/path/1/to/something";
        $segments = explode('/', trim($path, '/'));

        $this->assertEquals(
            $this->canonicalUrlWithPath($path),
            $this->urlResolver->resolve($segments));
    }

    public function testFragmentAndQuery()
    {
        $url = $this->urlResolver->resolve(array('/path/to/nothing/?query=true#fragment'));

        $this->assertEquals('fragment', $url->getFragment());
        $this->assertEquals('query=true', $url->getQuery());
    }

    public function testDispatcher()
    {
        $old_value = \Config::get('concrete.seo.url_rewriting');
        \Config::set('concrete.seo.url_rewriting', false);

        $canonical_path = $this->canonicalUrl->getPath();

        $url = $this->urlResolver->resolve(array('test'));
        $this->assertNotNull($url);

        if (!is_null($url)) {
            $dispatcher = DISPATCHER_FILENAME;
            $this->assertEquals("{$dispatcher}/test", $url->getPath()->getRelativePath($canonical_path));
        }


        \Config::set('concrete.seo.url_rewriting', $old_value);
    }

}
