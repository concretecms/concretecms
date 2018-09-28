<?php
namespace Concrete\Core\Form\Control;

interface FormViewInterface extends ViewInterface
{
    /**
     * Whether the form control is required in the current form
     * @return bool
     */
    public function isRequired();

    /**
     * Returns the label for the current form control.
     * @return string
     */
    public function getLabel();

    /**
     * Sets the label for the current form control.
     * @param $label
     */
    public function setLabel($label);

    /**
     * Returns true if the current form control supports labeling.
     * @return bool
     */
    public function supportsLabel();

    /**
     * Returns the ID of the current form control – may return null if the form
     * control doesn't need it.
     * @return string|null
     */
    public function getControlID();
}
