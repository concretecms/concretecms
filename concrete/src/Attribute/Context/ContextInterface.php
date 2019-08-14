<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Form\Context\ContextInterface as FormContextInterface;

/**
 * @since 8.0.0
 */
interface ContextInterface extends FormContextInterface
{

    function getActions();
    /**
     * @since 8.2.0
     */
    function getControlTemplates();
    /**
     * @since 8.2.0
     */
    function render(Key $key, $value = null);

}
