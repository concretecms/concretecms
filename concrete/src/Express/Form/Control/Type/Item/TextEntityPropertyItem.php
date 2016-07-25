<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

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
