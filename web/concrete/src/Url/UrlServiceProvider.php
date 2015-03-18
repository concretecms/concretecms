<?php
namespace Concrete\Core\Url;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;
use Concrete\Core\Url\Resolver\PageUrlResolver;
use Concrete\Core\Url\Resolver\PathUrlResolver;

class UrlServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'url/manager',
            function () {
                $path_resolver = new PathUrlResolver();
                $manager = new ResolverManager('concrete.path', $path_resolver);
                $manager->addResolver('concrete.page', new PageUrlResolver());

                return $manager;
            });
    }

}
