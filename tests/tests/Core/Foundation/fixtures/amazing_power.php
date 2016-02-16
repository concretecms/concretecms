<?php
namespace Concrete\Package\AmazingPower;

class Controller extends \Package
{
    protected $pkgHandle = 'amazing_power';
    protected $pkgAutoloaderMapCoreExtensions = true;
    protected $pkgAutoloaderRegistries = array(
        'src/ElectricState' => '\ElectricState\AmazingPower',
    );
}
