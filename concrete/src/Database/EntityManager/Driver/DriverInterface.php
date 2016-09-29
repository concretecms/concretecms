<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

/**
 * Specify a namespace for a Doctrine ORM driver, as well as what kind of mapping driver it should be.
 * See: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/advanced-configuration.html#multiple-metadata-sources
 * Interface DriverInterface
 */
interface DriverInterface
{

    /**
     * @return MappingDriver
     */
    function getDriver();

    /**
     * @return string
     */
    function getNamespace();

}
