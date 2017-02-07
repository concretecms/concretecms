<?php
namespace Concrete\Core\Form\Control;

use Concrete\Core\Form\Context\ContextInterface;

interface ViewInterface
{
    function render(ContextInterface $context);
}
