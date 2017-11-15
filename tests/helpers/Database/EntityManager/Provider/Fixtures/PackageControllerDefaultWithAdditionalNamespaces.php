<?php

namespace Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Package;

/**
 * Controller test addon - testing metadatadriver with default annotation driver
 * and additional namespaces.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerDefaultWithAdditionalNamespaces extends Package
{
    protected $pkgHandle = 'test_metadatadriver_additional_namespace';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';
    protected $pkgEnableLegacyNamespace = false;

    // The value we want to test
    protected $pkgAutoloaderRegistries = [
        'src/PortlandLabs/Concrete5/MigrationTool' => '\PortlandLabs\Concrete5\MigrationTool',
        'src/Dummy' => '\Dummy',
    ];

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
