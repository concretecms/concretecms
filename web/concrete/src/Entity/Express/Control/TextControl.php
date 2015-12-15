<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\Form\Control\Form\TextEntityPropertyControlFormRenderer;
use Concrete\Core\Express\Form\Control\Type\EntityPropertyType;
use Concrete\Core\Express\Form\Control\View\TextEntityPropertyControlViewRenderer;
use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetTextControls")
 */
class TextControl extends Control
{
    /**
     * @Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


    public function getFormRenderer(BaseEntity $entity = null)
    {
        return new TextEntityPropertyControlFormRenderer($entity);
    }

    public function getViewRenderer(BaseEntity $entity)
    {
        return new TextEntityPropertyControlViewRenderer($entity);
    }

    public function getControlLabel()
    {
        return t('Text');
    }

    public function getType()
    {
        return 'entity_property';
    }
}