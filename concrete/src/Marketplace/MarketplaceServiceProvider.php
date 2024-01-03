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

        $this->app->when(PackageRepository::class)->needs(Serializer::class)->give(function () {
            return new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        });
        $this->app->when(PackageRepository::class)->needs('$baseUri')->give(function () {
            return $this->app->make(Repository::class)->get('concrete.urls.package_repository');
        });
        $this->app->when(PackageRepository::class)->needs('$paths')->give(function () {
            return $this->app->make(Repository::class)->get('concrete.urls.paths.package_repository');
        });
    }
}
