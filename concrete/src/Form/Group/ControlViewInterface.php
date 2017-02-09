<?php
namespace Concrete\Core\Form\Group;

use Concrete\Core\Form\Control\ControlInterface;

interface ControlViewInterface extends ViewInterface
{
    function isRequired();
    function getLabel();
    function setLabel($label);
    function supportsLabel();
}
