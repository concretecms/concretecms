<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Form\Context\ContextInterface as FormContextInterface;

interface ContextInterface extends FormContextInterface
{

    function getActions();
    function getControlTemplates();

}
