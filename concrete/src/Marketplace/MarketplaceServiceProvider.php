<?php

namespace Concrete\Core\Marketplace;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Service\File;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Site\Service;
use GuzzleHttp\Client;
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

            return new PackageRepository(
                $this->app->make(Client::class),
                new Serializer([new ObjectNormalizer()], [new JsonEncoder()]),
                $config,
                $this->app->make('config/database'),
                $this->app->make(Service::class),
                $this->app->make(File::class),
                $config->get('concrete.urls.package_repository'),
                $config->get('concrete.urls.paths.package_repository')
            );
        });
    }
}
