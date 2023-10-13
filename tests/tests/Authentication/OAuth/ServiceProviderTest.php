<?php

namespace Concrete\Tests\Authentication\OAuth;

use Concrete\Core\Application\Application;
use Concrete\Core\Authentication\Type\OAuth\ServiceProvider;
use Concrete\Core\Routing\RoutingServiceProvider;
use Concrete\TestHelpers\Authentication\OAuth\Fixture\ExtractorFixture;
use Concrete\TestHelpers\Authentication\OAuth\Fixture\ServiceFixture;
use Concrete\Tests\TestCase;
use OAuth\UserData\ExtractorFactoryInterface;
use OAuth\UserData\Extractor\ExtractorInterface;

class ServiceProviderTest extends TestCase
{
    public function testBindings()
    {
        $app = new Application();

        (new RoutingServiceProvider($app))->register();
        $provider = new ServiceProvider($app);
        $provider->register();

        $service = new ServiceFixture();

        /** @var \OAuth\UserData\ExtractorFactory $extractor_factory */
        $extractor_factory = $app['oauth/factory/extractor'];
        $this->assertInstanceOf(ExtractorFactoryInterface::class, $extractor_factory);

        $extractor_factory->addExtractorMapping(
            ServiceFixture::class,
            ExtractorFixture::class
        );

        $this->assertInstanceOf(ExtractorInterface::class, $app->make('oauth_extractor', [$service]));
    }
}
