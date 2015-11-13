<?php

namespace Concrete\Core\Url;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;
use Concrete\Core\Url\Resolver\PageUrlResolver;
use Concrete\Core\Url\Resolver\PathUrlResolver;
use Concrete\Core\Url\Resolver\RouteUrlResolver;

class UrlServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->singleton(
            'url/canonical/resolver',
            function () {
                return new CanonicalUrlResolver();
            }
        );

        $this->app->singleton(
            'url/canonical',
            function () {
                return \Core::make('url/canonical/resolver')->resolve(array());
            }
        );

        $this->app->bindShared(
            'url/resolver/path',
            function () {
                return new PathUrlResolver();
            }
        );

        $this->app->bindShared(
            'url/resolver/page',
            function () {
                return new PageUrlResolver(\Core::make('url/resolver/path'));
            }
        );

        $this->app->bindShared(
            'url/resolver/route',
            function () {
                $generator = \Route::getGenerator();
                $list = \Route::getList();

                return new RouteUrlResolver(\Core::make('url/resolver/path'), $generator, $list);
            }
        );

        $this->app->bind(
            'url/manager',
            function () {
                $manager = new ResolverManager('concrete.path', \Core::make('url/resolver/path'));
                $manager->addResolver('concrete.page', \Core::make('url/resolver/page'));
                $manager->addResolver('concrete.route', \Core::make('url/resolver/route'));

                return $manager;
            }
        );
    }
}
