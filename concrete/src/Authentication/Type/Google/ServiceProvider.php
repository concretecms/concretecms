<?php
namespace Concrete\Core\Authentication\Type\Google;

use Concrete\Core\Application\Application;
use Concrete\Core\Authentication\Type\Google\Extractor\Google as GoogleExtractor;
use Config;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession;
use OAuth\OAuth2\Service\Google;
use OAuth\ServiceFactory;
use OAuth\UserData\ExtractorFactory;

class ServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        /* @var ExtractorFactory $factory */
        $extractor = $this->app->make('oauth/factory/extractor');
        $extractor->addExtractorMapping(Google::class, GoogleExtractor::class);
    }
}
