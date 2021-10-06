<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

class AuthorEntityPropertyItem implements ItemInterface
{
    public function getDisplayName()
    {
        return t('Author');
    }

    public function getIcon()
    {
        return '<i class="fas fa-user"></i>';
    }

    public function getItemIdentifier()
    {
        return 'author';
    }
}
