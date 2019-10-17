<?php
namespace Concrete\Core\Express\Form\Control\Type\Item;

class PublicIdentifierPropertyItem implements ItemInterface
{
    public function getDisplayName()
    {
        return t('Public Identifier');
    }

    public function getIcon()
    {
        return '<i class="fa fa-barcode"></i>';
    }

    public function getItemIdentifier()
    {
        return 'public-identifier';
    }
}
