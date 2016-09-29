<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Driver\DriverInterface;

/**
 * Implement this in your package controller if you'd like to provide a custom entity manager.
 * Interface ProviderAggregateInterface
 */
interface ProviderAggregateInterface
{

    /**
     * @return ProviderInterface
     */
    function getEntityManagerProvider();


}
