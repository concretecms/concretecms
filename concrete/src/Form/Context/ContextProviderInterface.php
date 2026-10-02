<?php
namespace Concrete\Core\Form\Context;

use Concrete\Core\Form\Context\Registry\ContextRegistryInterface;

interface ContextProviderInterface
{

    /**
     * @return ContextRegistryInterface|null
     */
    function getContextRegistry();

}
