<?php
namespace Concrete\Core\Sharing;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Sharing\OpenGraph\OpenGraph;
use Concrete\Core\Sharing\SocialNetwork\LinkUrlValidator;

class SharingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OpenGraph::class);
        $this->app->bind(LinkUrlValidator::class, static function ($app):LinkUrlValidator  {
            return new LinkUrlValidator((array) $app->make(Repository::class)->get('concrete.social.additional_schemes', []));
        });
    }
}
