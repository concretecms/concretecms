<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Form\Control\ControlInterface;

interface FormInterface extends ControlInterface
{

    function getFieldSets();
    function getId();

}
