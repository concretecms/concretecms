<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Form\Group\ViewInterface as GroupViewInterface;

interface ControlInterface
{
    function getControlView(ContextInterface $context);
}
