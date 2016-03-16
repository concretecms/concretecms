<?php
namespace Concrete\Package\DummyOther;

use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Dummy package controller for the translation related tests.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class Controller extends Package
{

    protected $pkgHandle = 'dummy_other';
    protected $appVersionRequired = '5.7.5.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageName()
    {
        return t("Dummy Other");
    }

    public function getPackageDescription()
    {
        return "";
    }

}