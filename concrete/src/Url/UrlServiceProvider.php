<?php
namespace Concrete\Core\Url;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class UrlServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->singleton('Concrete\Core\Url\Resolver\CanonicalUrlResolver');
        $this->app->bind('url/canonical/resolver', '\Concrete\Core\Url\Resolver\CanonicalUrlResolver');

        $this->app->bind('url/canonical', function ($app) {
            return $app->make('Concrete\Core\Url\Resolver\CanonicalUrlResolver')->resolve(array());
        });

        // Share the path url resolver
        $this->app->singleton('Concrete\Core\Url\Resolver\PathUrlResolver');
        $this->app->bind('url/resolver/path', 'Concrete\Core\Url\Resolver\PathUrlResolver');

        // Share the Page url resolver
        $this->app->singleton('Concrete\Core\Url\Resolver\PageUrlResolver');
        $this->app->bind('url/resolver/page', 'Concrete\Core\Url\Resolver\PageUrlResolver');

        // Share the route url resolver
        $this->app->singleton('Concrete\Core\Url\Resolver\RouterUrlResolver');
        $this->app->bind('url/resolver/route', 'Concrete\Core\Url\Resolver\RouterUrlResolver');

        $this->app->bindShared('Concrete\Core\Url\Resolver\Manager\ResolverManager',
            function ($app, $default_handle = '', $default_resolver = null) {
                $manager = new ResolverManager($default_handle ?: 'concrete.path', $default_resolver);

                $manager->addResolver('concrete.path', $app->make('Concrete\Core\Url\Resolver\PathUrlResolver'));
                $manager->addResolver('concrete.page', $app->make('Concrete\Core\Url\Resolver\PageUrlResolver'));
                $manager->addResolver('concrete.route', $app->make('Concrete\Core\Url\Resolver\RouterUrlResolver'));

                return $manager;
            });
        $this->app->bind('Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface', 'Concrete\Core\Url\Resolver\Manager\ResolverManager');
        $this->app->bind('url/manager', 'Concrete\Core\Url\Resolver\Manager\ResolverManager');
    }
}
