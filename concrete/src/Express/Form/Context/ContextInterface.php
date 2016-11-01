<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Entity\Express\Control\Control;

interface ContextInterface
{
    function getApplication();
    function getFormRenderer();
    function getContextHandle();
}
