<?php
namespace Concrete\Core\Form\Context;

use Concrete\Core\Form\Control\ControlInterface;

interface ContextInterface
{
    function getFormGroupView(ControlInterface $control);
}
