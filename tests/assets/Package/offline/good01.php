<?php

defined('C5_EXECUTE') or die('Access Denied.');

class NiceLegacyPackage extends Package
{
    protected $pkgHandle = 'nice_legacy';
    protected $appVersionRequired = '5.5';
    protected $pkgVersion = '0.9.1';

    public function getPackageName()
    {
        return t('Legacy package!');
    }

    public function getPackageDescription()
    {
        return t('This is a nice legacy package.');
    }
}
