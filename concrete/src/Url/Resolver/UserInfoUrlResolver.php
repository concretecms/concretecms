<?php

namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Application\Application;
use Concrete\Core\User\UserInfo;

class UserInfoUrlResolver implements UrlResolverInterface
{
    /**
     * @var \Concrete\Core\Url\Resolver\PathUrlResolver
     */
    protected $pathUrlResolver;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Url\Resolver\PathUrlResolver $path_url_resolver
     */
    public function __construct(Application $app, PathUrlResolver $path_url_resolver)
    {
        $this->app = $app;
        $this->pathUrlResolver = $path_url_resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\UrlResolverInterface::resolve()
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if ($resolved) {
            return $resolved;
        }

        $user = $arguments ? head($arguments) : null;

        if ($user instanceof UserInfo) {
            $site = $this->app->make('site')->getSite();
            if ($site) {
                $config = $site->getConfigRepository();
                if ($config->get('user.profiles_enabled')) {
                    return $user->getUserPublicProfileUrl();
                }
            }

            return $this->pathUrlResolver->resolve(['/dashboard/users/search', 'view', $user->getUserID()]);
        }

        return null;
    }
}
