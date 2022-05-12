<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Request;
use Doctrine\ORM\EntityManagerInterface;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use IPLib\ParseStringFlag as IPParseStringFlag;

class PermissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $this->app->singleton('Concrete\Core\Permission\Inheritance\Registry\BlockRegistry');

        $this->app
            ->when(IpAccessControlService::class)
            ->needs(Site::class)
            ->give(function (Application $app) {
                return $app->make('site')->getSite();
            }
        );

        $this->app->bind(AddressInterface::class, function (Application $app) {
            if ($app->isRunThroughCommandLineInterface()) {
                $ip = '127.0.0.1';
            } else {
                $request = $this->app->make(Request::class);
                $ip = $request->getClientIp();
            }

            return IPFactory::parseAddressString($ip, IPParseStringFlag::IPV4_MAYBE_NON_DECIMAL | IPParseStringFlag::IPV4ADDRESS_MAYBE_NON_QUAD_DOTTED | IPParseStringFlag::MAY_INCLUDE_PORT | IPParseStringFlag::MAY_INCLUDE_ZONEID);
        });

        $this->app->bind('failed_login', function (Application $app) {
            $em = $app->make(EntityManagerInterface::class);
            $repo = $em->getRepository(IpAccessControlCategory::class);
            $category = $repo->findOneBy(['handle' => 'failed_login']);

            return $app->make(IpAccessControlService::class, ['category' => $category]);
        });

        $this->app->bind('ip/access/control/forgot_password', function (Application $app) {
            $em = $app->make(EntityManagerInterface::class);
            $repo = $em->getRepository(IpAccessControlCategory::class);
            $category = $repo->findOneBy(['handle' => 'forgot_password']);
            return $app->make(IpAccessControlService::class, ['category' => $category]);
        });

        $this->app->singleton('permission/access/entity/factory', function () use ($app) {
            return new Access\Entity\Factory($app);
        });
    }
}
