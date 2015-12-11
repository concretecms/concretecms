<?php

namespace Concrete\Core\Express\Form\Control\Type\Item;

use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Control\Control;

class NameEntityPropertyItem implements ItemInterface
{

    public function getDisplayName()
    {
        return t('Name');
    }

    public function getIcon()
    {
        return '<i class="fa fa-header"></i>';
    }

    public function getItemIdentifier()
    {
        return 'name';
    }

}