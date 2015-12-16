<?php

namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\EntityNameControl;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Control\Type\Item\NameEntityPropertyItem;
use Concrete\Core\Express\Form\Control\Type\Item\TextEntityPropertyItem;

class EntityPropertyType implements TypeInterface {

    public function getPluralDisplayName()
    {
        return t('Core Properties');
    }

    public function getType()
    {
        return 'entity_property';
    }

    public function supportsValidation()
    {
        return false;
    }

    public function getDisplayName()
    {
        return t('Core Property');
    }

    public function getItems(Entity $entity)
    {
        return array(
            new TextEntityPropertyItem()
        );
    }

    public function createControlByIdentifier($id)
    {
        switch($id) {
            case 'text':
                return new TextControl();
        }
    }

    public function getSaveHandler(Control $control)
    {
        return null;
    }



}
