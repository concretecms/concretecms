<?php

namespace Concrete\Tests\User;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Mockery as M;
use Symfony\Component\HttpFoundation\Session\Session;

class PostLoginLocationTest extends \PHPUnit_Framework_TestCase
{

    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var \Illuminate\Contracts\Config\Repository|\Mockery\MockInterface */
    protected $config;

    /** @var Session|\Mockery\MockInterface */
    protected $session;

    /** @var ResolverManagerInterface|\Mockery\MockInterface */
    protected $resolver;

    /** @var ResponseFactoryInterface|\Mockery\MockInterface */
    protected $responseFactory;

    /** @var Numbers|\Mockery\MockInterface */
    protected $numberValidator;

    /** @var \Concrete\Core\Site\Service|\Mockery\MockInterface */
    protected $siteService;

    /** @var PostLoginLocation|\Mockery\MockInterface */
    protected $location;

    /**
     * @before
     */
    public function prepare()
    {
        $this->config = M::mock(Repository::class);
        $this->session = M::mock(Session::class);
        $this->resolver = M::mock(ResolverManagerInterface::class);
        $this->responseFactory = M::mock(ResponseFactoryInterface::class);
        $this->numberValidator = M::mock(Numbers::class);
        $this->siteService = M::mock(Service::class);

        $this->location = new PostLoginLocation(
            $this->config,
            $this->session,
            $this->resolver,
            $this->responseFactory,
            $this->numberValidator,
            $this->siteService);
    }

    /**
     * @after
     */
    public function destroy()
    {
        $this->config =
        $this->session =
        $this->resolver =
        $this->responseFactory =
        $this->numberValidator =
        $this->siteService =
        $this->location = null;
    }

    public function testGetFallbackPostLoginUrl()
    {
        // Set up a fake site to be resolved
        $fakeSite = M::mock(Site::class);
        $this->siteService->shouldReceive('getSite')->twice()->andReturn($fakeSite);

        // Set up a fake homepage for our fake site
        $fakePage = M::mock(Page::class);
        $fakeSite->shouldReceive('getSiteHomePageObject')->andReturn($fakePage);

        // First test what happens when the page has an error, then test when it doesn't have one.
        $fakePage->shouldReceive('isError')->andReturnValues([true, false]);

        // Set up the resolver manager to resolve both error and non error
        $fallbackHomePath = 'Site homepage had an error';
        $siteHomePath = 'Real site homepath';

        $this->resolver->shouldReceive('resolve')->with(['/'])->andReturn($fallbackHomePath);
        $this->resolver->shouldReceive('resolve')->with([$fakePage])->andReturn($siteHomePath);

        $this->assertEquals($fallbackHomePath, $this->location->getFallbackPostLoginUrl());
        $this->assertEquals($siteHomePath, $this->location->getFallbackPostLoginUrl());
    }
}
