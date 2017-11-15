<?php

namespace Concrete\Tests\Core\Authentication;

use Concrete\Core\Application\Application;
use Concrete\Core\Authentication\Type\OAuth\ServiceProvider;

/** @TODO Set up proper autoloading for tests */
require_once(__DIR__ . "/Fixtures/ServiceFixture.php");
require_once(__DIR__ . "/Fixtures/ExtractorFixture.php");
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testBindings()
    {
        $app = new Application();

        $provider = new ServiceProvider($app);
        $provider->register();

        $service = new \Concrete\Tests\Core\Authentication\Fixtures\ServiceFixture;

        /** @var \OAuth\UserData\ExtractorFactory $extractor_factory */
        $extractor_factory = $app['oauth/factory/extractor'];
        $this->assertInstanceOf('OAuth\UserData\ExtractorFactoryInterface', $extractor_factory);

        $extractor_factory->addExtractorMapping(
            'Concrete\Tests\Core\Authentication\Fixtures\ServiceFixture',
            'Concrete\Tests\Core\Authentication\Fixtures\ExtractorFixture');

        $this->assertInstanceOf('OAuth\UserData\Extractor\ExtractorInterface', $app->make('oauth_extractor', array($service)));
    }

}
