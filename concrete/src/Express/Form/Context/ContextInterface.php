<?php
namespace Concrete\Core\Express\Form\Context;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\FormInterface;
use Concrete\Core\Form\Context\ContextInterface as BaseContextInterface;

/**
 * @since 8.0.0
 */
interface ContextInterface extends BaseContextInterface
{

    /**
     * @since 8.2.0
     */
    function getAttributeContext();
    /**
     * @since 8.2.0
     */
    function getEntry();
    /**
     * @since 8.2.0
     */
    function setEntry(Entry $entry);
    /**
     * @since 8.2.0
     */
    function setForm(FormInterface $form);
    /**
     * @since 8.2.0
     */
    function getForm();

}
