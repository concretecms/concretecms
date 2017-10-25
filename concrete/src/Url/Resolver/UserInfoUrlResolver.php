<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Application\Application;
use Concrete\Core\Url\Url;
use Concrete\Core\User\UserInfo;

class UserInfoUrlResolver implements UrlResolverInterface
{
    /** @var UrlResolverInterface */
    protected $pathUrlResolver;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, PathUrlResolver $path_url_resolver)
    {
        $this->app = $app;
        $this->pathUrlResolver = $path_url_resolver;
    }

    public function resolve(array $arguments, $resolved = null)
    {
        if ($resolved) {
            return $resolved;
        }

        if ($arguments) {
            $user = head($arguments);
        }

        if (isset($user) && $user instanceof UserInfo) {
            $site = $this->app->make('site')->getSite();
            if ($site) {
                $config = $site->getConfigRepository();
                if ($config->get('user.profiles_enabled')) {
                    return $user->getUserPublicProfileUrl();
                }
            }
            return $this->pathUrlResolver->resolve(['/dashboard/users/search', 'view', $user->getUserID()]);
        }

    }
}
