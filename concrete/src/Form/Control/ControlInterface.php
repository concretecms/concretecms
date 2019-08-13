<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Form\Context\ContextInterface;

/**
 * @since 8.2.0
 */
interface ControlInterface
{
    function getControlView(ContextInterface $context);
}
