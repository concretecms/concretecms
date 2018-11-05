<?php

namespace Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Package;

/**
 * Controller test addon - testing metadatadriver with default annotation driver.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerDefault extends Package
{
    protected $pkgHandle = 'test_metadatadriver_default';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';
    protected $pkgEnableLegacyNamespace = false;

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function getPackageDescription()
    {
        return t('Test addon registers entities via standard annotation driver');
    }

    public function getPackageName()
    {
        return t('Test addon - uses standard annotation driver');
    }
}
