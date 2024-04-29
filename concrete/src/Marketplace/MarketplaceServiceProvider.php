<?php

namespace Concrete\Core\Marketplace;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MarketplaceServiceProvider extends Provider
{
    public function register()
    {
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(PackageRepository::class, function(): PackageRepository {
            $config = $this->app->make(Repository::class);
            return $this->app->make(PackageRepository::class, [
                'serializer' => new Serializer([new ObjectNormalizer()], [new JsonEncoder()]),
                'config' => $config,
                'databaseConfig' => $this->app->make('config/database'),
                'baseUri' => $config->get('concrete.urls.package_repository'),
                'paths' => $config->get('concrete.urls.paths.package_repository'),
            ]);
        });
    }
}
