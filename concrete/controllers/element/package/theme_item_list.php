<?php
namespace Concrete\Controller\Element\Package;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\ItemInterface;

class ThemeItemList extends ItemList
{

    public function getElement()
    {
        return 'package/theme_item_list';
    }

}
