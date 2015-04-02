<?php
namespace Concrete\Core\Url;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
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
        $this->app->singleton(
            'url/canonical/resolver',
            function () {
                return new CanonicalUrlResolver();
            });

        $this->app->singleton(
            'url/canonical',
            function () {
                return \Core::make('url/canonical/resolver')->resolve(array());
            });

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
