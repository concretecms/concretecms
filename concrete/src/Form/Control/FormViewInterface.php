<?php
namespace Concrete\Core\Form\Control;

interface FormViewInterface extends ViewInterface
{
    function isRequired();
    function getLabel();
    function setLabel($label);
    function supportsLabel();
}
