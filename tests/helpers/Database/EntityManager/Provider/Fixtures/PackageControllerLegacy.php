<?php

namespace Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Package;

/**
 * Controller test addon - testing metadatadriver with default annotation driver.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerLegacy extends Package
{
    protected $pkgHandle = 'test_metadatadriver_legacy';
    protected $appVersionRequired = '5.7.0';
    protected $pkgVersion = '0.0.1';
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
}
