<?php
namespace Concrete\Controller\Element\Package;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\ItemInterface;

class SinglePagesItemList extends ItemList
{

    public function getElement()
    {
        return 'package/single_pages_item_list';
    }

}
