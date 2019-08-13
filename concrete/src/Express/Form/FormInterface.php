<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Form\Control\ControlInterface;

/**
 * @since 8.0.0
 */
interface FormInterface extends ControlInterface
{

    function getFieldSets();
    function getId();

}
