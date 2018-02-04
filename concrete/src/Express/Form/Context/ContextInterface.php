<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\FormInterface;
use Concrete\Core\Form\Context\ContextInterface as BaseContextInterface;

interface ContextInterface extends BaseContextInterface
{

    function getAttributeContext();
    function getEntry();
    function setEntry(Entry $entry);
    function setForm(FormInterface $form);
    function getForm();

}
