<?php

namespace Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Package\Package;
use Concrete\Core\Application\Application;

/**
 * Controller test addon - testing metadatadriver with default annotation driver
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerDefault extends Package
{

    protected $pkgHandle = 'test_metadatadriver_default';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function getPackageDescription()
    {
        return t("Test addon registers entities via standard annotation driver");
    }

    public function getPackageName()
    {
        return t("Test addon - uses standard annotation driver");
    }

}
