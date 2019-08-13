<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

/**
 * @since 8.0.0
 */
class TextEntityPropertyItem implements ItemInterface
{
    public function getDisplayName()
    {
        return t('Text');
    }

    public function getIcon()
    {
        return '<i class="fa fa-file-text"></i>';
    }

    public function getItemIdentifier()
    {
        return 'text';
    }
}
