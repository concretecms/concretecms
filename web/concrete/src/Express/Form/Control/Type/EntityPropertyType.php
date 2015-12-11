<?php

namespace Concrete\Core\Express\Form\Control\Type;

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

    public function getItems(Entity $entity)
    {
        return array(
            new NameEntityPropertyItem(),
            new TextEntityPropertyItem()
        );
    }

    public function createControlByIdentifier($id)
    {
        switch($id) {
            case 'name':
                return new EntityNameControl();
            case 'text':
                return new TextControl();
        }
    }

}
