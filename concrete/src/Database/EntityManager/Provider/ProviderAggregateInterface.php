<?php
namespace Concrete\Core\Database\EntityManager\Provider;

/**
 * Implement this in your package controller if you'd like to provide a custom entity manager.
 * Interface ProviderAggregateInterface
 * @since 8.0.0
 */
interface ProviderAggregateInterface
{

    /**
     * @return ProviderInterface
     */
    function getEntityManagerProvider();
}
