<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

class NameEntityPropertyItem implements ItemInterface
{
    public function getDisplayName()
    {
        return t('Name');
    }

    public function getIcon()
    {
        return '<i class="fas fa-heading"></i>';
    }

    public function getItemIdentifier()
    {
        return 'name';
    }
}
