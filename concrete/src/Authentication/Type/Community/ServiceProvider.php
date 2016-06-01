<?php
namespace Concrete\Core\Authentication\Type\Community;

use Concrete\Core\Authentication\Type\Community\Extractor\Community as CommunityExtractor;
use Concrete\Core\Authentication\Type\Community\Service\Community;
use OAuth\UserData\ExtractorFactory;

class ServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        /** @var ExtractorFactory $extractor */
        $extractor = $this->app->make('oauth/factory/extractor');
        $extractor->addExtractorMapping(Community::class, CommunityExtractor::class);
    }
}
