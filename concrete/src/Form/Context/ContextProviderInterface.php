<?php
namespace Concrete\Core\Form\Context;

use Concrete\Core\Form\Context\Registry\ContextRegistryInterface;

/**
 * @since 8.2.0
 */
interface ContextProviderInterface
{

    /**
     * @return ContextRegistryInterface
     */
    function getContextRegistry();

}
