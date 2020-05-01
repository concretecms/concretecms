<?php

namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;

class PackageServiceProvider extends Provider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        $this->app
            ->when(Offline\Inspector::class)
            ->needs('$parsers')
            ->give(function (Application $app) {
                return [
                    $app->make(Offline\Parser\Legacy::class),
                    $app->make(Offline\Parser\FiveSeven::class),
                ];
            })
        ;
    }
}
