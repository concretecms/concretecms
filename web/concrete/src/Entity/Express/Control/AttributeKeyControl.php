<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\Form\Control\Form\AttributeKeyControlFormRenderer;
use Concrete\Core\Express\Form\Control\View\AttributeKeyControlViewRenderer;
use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetAttributeKeyControls")
 */
class AttributeKeyControl extends Control
{
    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     */
    protected $attribute_key;

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute_key
     */
    public function setAttributeKey($attribute_key)
    {
        $this->attribute_key = $attribute_key;
    }

    public function getFormRenderer(BaseEntity $entity = null)
    {
        return new AttributeKeyControlFormRenderer($entity);
    }

    public function getViewRenderer(BaseEntity $entity)
    {
        return new AttributeKeyControlViewRenderer($entity);
    }

    public function getControlLabel()
    {
        return $this->getAttributeKey()->getAttributeKeyDisplayName();
    }

    public function getType()
    {
        return 'attribute_key';
    }

}