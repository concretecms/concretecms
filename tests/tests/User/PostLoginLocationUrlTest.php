<?php

namespace Concrete\Tests\User;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Concrete\Core\User\PostLoginLocationUrl;
use Mockery as M;
use Concrete\Tests\TestCase;

class PostLoginLocationUrlTest extends TestCase
{
    /** @var \Concrete\Core\Config\Repository\Repository|\Mockery\MockInterface */
    protected $config;

    /** @var CanonicalUrlResolver|\Mockery\MockInterface */
    protected $canonicalUrlResolver;

    /** @var PostLoginLocationUrl */
    protected $urlHelper;

    /**
     * @before
     */
    public function prepare(): void
    {
        $this->config = M::mock(Repository::class);
        $this->canonicalUrlResolver = M::mock(CanonicalUrlResolver::class);
        $this->urlHelper = new PostLoginLocationUrl($this->config, $this->canonicalUrlResolver);
    }

    /**
     * @after
     */
    public function destroy(): void
    {
        $this->config = null;
        $this->canonicalUrlResolver = null;
        $this->urlHelper = null;
    }

    public function testGetAllowedRedirectUrlUsesCanonicalBaseByDefault(): void
    {
        $this->config->shouldReceive('get')->with('concrete.security.post_login_redirect_url_allowlist', [])->once()->andReturn([]);
        $this->canonicalUrlResolver->shouldReceive('resolve')->with([])->once()->andReturn('https://login.example.com');

        $this->assertSame(
            'https://login.example.com/account',
            $this->urlHelper->getAllowedRedirectUrl('https://login.example.com/account')
        );
    }

    public function testGetAllowedRedirectUrlRejectsDisallowedAbsoluteUrl(): void
    {
        $this->config->shouldReceive('get')->with('concrete.security.post_login_redirect_url_allowlist', [])->once()->andReturn([]);
        $this->canonicalUrlResolver->shouldReceive('resolve')->with([])->once()->andReturn('https://login.example.com');

        $this->assertSame(
            '',
            $this->urlHelper->getAllowedRedirectUrl('https://evil.example.com/account')
        );
    }

    public function testAllowedRedirectUrlsUseConfiguredAllowlist(): void
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
