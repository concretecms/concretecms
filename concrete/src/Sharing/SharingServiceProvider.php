<?php
namespace Concrete\Core\Sharing;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Sharing\OpenGraph\OpenGraph;

class SharingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OpenGraph::class);
    }
}
