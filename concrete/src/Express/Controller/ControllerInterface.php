<?php
namespace Concrete\Core\Express\Controller;

use Concrete\Core\Form\Context\ContextProviderInterface;

interface ControllerInterface extends ContextProviderInterface
{

    function getContextRegistry();
    function getFormProcessor();



}
