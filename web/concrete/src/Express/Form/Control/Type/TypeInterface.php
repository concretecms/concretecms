<?php

namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;

interface TypeInterface
{

    public function getPluralDisplayName();
    public function getDisplayName();
    public function getItems(Entity $entity);
    public function createControlByIdentifier($id);
    public function getSaveHandler(Control $control);

}