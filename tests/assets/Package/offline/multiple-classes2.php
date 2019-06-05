<?php

namespace Concrete\Package\NiceModern;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'nice_modern';
    protected $appVersionRequired = '8.5';
    protected $pkgVersion = 1.2;

    public function getPackageName()
    {
        return t('5.7+ package!');
    }

    public function getPackageDescription()
    {
        return t('This is a nice 5.7+ package.');
    }
}

class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'nice_modern';
    protected $appVersionRequired = '8.5';
    protected $pkgVersion = 1.2;

    public function getPackageName()
    {
        return t('5.7+ package!');
    }

    public function getPackageDescription()
    {
        return t('This is a nice 5.7+ package.');
    }
}
