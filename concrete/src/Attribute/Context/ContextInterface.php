<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Form\Context\ContextInterface as FormContextInterface;

interface ContextInterface extends FormContextInterface
{

    function getActions();
    function getControlTemplates();
    function render(Key $key, $value = null);

}
