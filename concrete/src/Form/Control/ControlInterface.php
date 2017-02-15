<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Form\Context\ContextInterface;

interface ControlInterface
{
    function getControlView(ContextInterface $context);
}
