<?php

namespace Concrete\Tests\User;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Concrete\Core\User\PostLocationLocationUrl;
use Mockery as M;
use Concrete\Tests\TestCase;

class PostLocationLocationUrlTest extends TestCase
{
    /** @var \Concrete\Core\Config\Repository\Repository|\Mockery\MockInterface */
    protected $config;

    /** @var CanonicalUrlResolver|\Mockery\MockInterface */
    protected $canonicalUrlResolver;

    /** @var PostLocationLocationUrl */
    protected $urlHelper;

    /**
     * @before
     */
    public function prepare()
    {
        $this->config = M::mock(Repository::class);
        $this->canonicalUrlResolver = M::mock(CanonicalUrlResolver::class);
        $this->urlHelper = new PostLocationLocationUrl($this->config, $this->canonicalUrlResolver);
    }

    /**
     * @after
     */
    public function destroy()
    {
        $this->config = null;
        $this->canonicalUrlResolver = null;
        $this->urlHelper = null;
    }

    public function testGetAllowedRedirectUrlUsesCanonicalBaseByDefault()
    {
        $this->config->shouldReceive('get')->with('concrete.security.post_login_redirect_url_allowlist', [])->once()->andReturn([]);
        $this->canonicalUrlResolver->shouldReceive('resolve')->with([])->once()->andReturn('https://login.example.com');

        $this->assertSame(
            'https://login.example.com/account',
            $this->urlHelper->getAllowedRedirectUrl('https://login.example.com/account')
        );
    }

    public function testGetAllowedRedirectUrlRejectsDisallowedAbsoluteUrl()
    {
        $this->config->shouldReceive('get')->with('concrete.security.post_login_redirect_url_allowlist', [])->once()->andReturn([]);
        $this->canonicalUrlResolver->shouldReceive('resolve')->with([])->once()->andReturn('https://login.example.com');

        $this->assertSame(
            '',
            $this->urlHelper->getAllowedRedirectUrl('https://evil.example.com/account')
        );
    }

    public function testAllowedRedirectUrlsUseConfiguredAllowlist()
    {
        $this->config->shouldReceive('get')->with('concrete.security.post_login_redirect_url_allowlist', [])->times(3)->andReturn([
            'https://accounts.example.com',
            'https://network.example.com/sso',
        ]);
        $this->canonicalUrlResolver->shouldNotReceive('resolve');

        $this->assertSame(
            'https://accounts.example.com/profile',
            $this->urlHelper->getAllowedRedirectUrl('https://accounts.example.com/profile')
        );
        $this->assertSame(
            'https://network.example.com/sso/complete?ticket=1',
            $this->urlHelper->getAllowedRedirectUrl('https://network.example.com/sso/complete?ticket=1')
        );
        $this->assertSame(
            '',
            $this->urlHelper->getAllowedRedirectUrl('https://network.example.com/sso-provider')
        );
    }
}
