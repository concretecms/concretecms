<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Driver\DriverInterface;

/**
 * Anything implementing the Provider interface is able to delivery one or more entity manager drivers. Currently
 * the Concrete\Core\Package\Package class (extended by all package controllers) is the only object that implements
 * this interface.
 * Interface ProviderInterface
 */
interface ProviderInterface
{

    /**
     * @return DriverInterface[]
     */
    function getDrivers();
}
