<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

/**
 * @since 8.5.0
 */
class AuthorEntityPropertyItem implements ItemInterface
{
    public function getDisplayName()
    {
        return t('Author');
    }

    public function getIcon()
    {
        return '<i class="fa fa-user"></i>';
    }

    public function getItemIdentifier()
    {
        return 'author';
    }
}
