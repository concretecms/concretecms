<?php

namespace Concrete\Package\AmazingPower;

class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'amazing_power';
    protected $pkgAutoloaderMapCoreExtensions = true;
    protected $pkgAutoloaderRegistries = [
        'src/ElectricState' => '\ElectricState\AmazingPower',
    ];
}
