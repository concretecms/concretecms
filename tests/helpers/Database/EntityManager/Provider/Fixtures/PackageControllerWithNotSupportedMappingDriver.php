<?php

namespace Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Package\Package;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;

/**
 * Controller test addon - testing metadatadriver which is not support -> custom EntityManager
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerWithNotSupportedMappingDriver extends Package implements ProviderAggregateInterface
{

    protected $pkgHandle = 'test_metadatadriver_php_array';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function getPackageDescription()
    {
        return t("Test addon registers entities via the xml driver");
    }

    public function getPackageName()
    {
        return t("Test addon - uses xml driver");
    }

    public function getEntityManagerProvider()
    {
        
    }

}
