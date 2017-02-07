<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Form\Group\ViewInterface as GroupViewInterface;

interface ControlInterface
{
    /**
     * @return ViewInterface
     */
    function getControlView();

    /**
     * @return GroupViewInterface
     */
    function getFormGroupView(ContextInterface $context);
}
