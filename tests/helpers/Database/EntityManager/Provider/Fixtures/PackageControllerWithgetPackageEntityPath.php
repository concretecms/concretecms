<?php

namespace Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Package;

/**
 * Controller test addon - testing legacy annotation driver
 * with deprecated getPackageEntityPath() method
 * and no adjustemens.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerWithgetPackageEntityPath extends Package
{
    protected $pkgHandle = 'test_metadatadriver_legacy_with_getpackageentitypath';
    protected $appVersionRequired = '5.7.0';
    protected $pkgVersion = '0.0.1';
    // just making jure that test will not break after version 8.1.0
    protected $pkgEnableLegacyNamespace = true;

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function getPackageDescription()
    {
        return t('Test addon registers entities via the xml driver');
    }

    public function getPackageName()
    {
        return t('Test addon - uses xml driver');
    }

    public function getPackageEntityPath()
    {
        // This file path should be ignored by the DefaultPackageProvider
        return $this->getRelativePath() . '/' . DIRNAME_CLASSES
                . '/Entities';
    }
}
