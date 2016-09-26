<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

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
