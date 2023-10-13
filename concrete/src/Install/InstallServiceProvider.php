<?php

namespace Concrete\Core\Install;

use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Install\Installer;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class InstallServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->when(Installer::class)
            ->needs(SerializerInterface::class)
            ->give(
                function () {
                    $serializer = new JsonSerializer(
                        [
                            new JsonSerializableNormalizer(),
                            new GetSetMethodNormalizer(),
                        ], [
                            new JsonEncoder()
                        ]
                    );
                    return $serializer;
                }
            );
        $this->app->when(Installer::class)
            ->needs(Request::class)
            ->give(
                function () {
                    return Request::createFromGlobals();
                }
            );
    }
}
