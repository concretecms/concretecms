<?php
namespace Concrete\Core\Form\Control;

abstract class FormView extends View implements FormViewInterface
{

    protected $label;
    protected $supportsLabel = true;
    protected $isRequired = false;

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * @param boolean $isRequired
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setSupportsLabel($supportsLabel)
    {
        $this->supportsLabel = $supportsLabel;
    }

    public function supportsLabel()
    {
        return $this->supportsLabel;
    }


}
