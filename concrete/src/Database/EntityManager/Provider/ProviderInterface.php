<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Driver\DriverInterface;

interface ProviderInterface
{

    /**
     * @return DriverInterface[]
     */
    function getDrivers();


}
