<?php
namespace Concrete\Core\Form\Group;

use Concrete\Core\Form\Control\ControlInterface;
use Concrete\Core\Form\Control\ValueInterface;

interface ViewInterface
{
    function isRequired();
    function getFieldWrapperTemplate();
    function getLabel();
    function setLabel($label);
    function supportsLabel();
    function render(ControlInterface $control, ValueInterface $value = null);
    function renderControl();
}
