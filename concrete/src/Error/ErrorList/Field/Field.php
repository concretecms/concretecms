<?php
namespace Concrete\Core\Error\ErrorList\Field;

class Field extends AbstractField
{

    protected $elementName;
    protected $displayName;

    /**
     * Field constructor.
     * @param $fieldName
     */
    public function __construct($elementName, $displayName = null)
    {
        $this->elementName = $elementName;
        $this->displayName = $displayName;

    }

    /**
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getFieldElementName()
    {
        return $this->elementName;
    }

    public function getDisplayName()
    {
        return $this->displayName ? $this->displayName : $this->elementName;
    }


}
