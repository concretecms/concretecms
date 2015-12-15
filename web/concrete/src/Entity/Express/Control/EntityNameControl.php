<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\Form\Control\Form\NameEntityPropertyControlFormRenderer;
use Concrete\Core\Express\Form\Control\Type\EntityPropertyType;
use Concrete\Core\Express\Form\Control\View\NameEntityPropertyControlViewRenderer;

/**
 * @Entity
 */
class EntityNameControl extends Control
{

    public function getFormRenderer(BaseEntity $entity = null)
    {
        return new NameEntityPropertyControlFormRenderer($entity);
    }

    public function getViewRenderer(BaseEntity $entity)
    {
        return new NameEntityPropertyControlViewRenderer($entity);
    }

    public function getControlLabel()
    {
        return t('Name');
    }

    public function getControlItemType()
    {
        return new EntityPropertyType();
    }

    public function getType()
    {
        return 'entity_property';
    }



}