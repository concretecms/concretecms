<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\Form\Control\NameEntityPropertyControlRenderer;
use Concrete\Core\Express\Form\Control\Type\EntityPropertyType;
use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 */
class EntityNameControl extends Control
{

    public function getFormRenderer()
    {
        return new NameEntityPropertyControlRenderer();
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