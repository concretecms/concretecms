<?php
namespace Concrete\Controller\Element\Package;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\ItemInterface;

class BlockTypeSetItemList extends ItemList
{

    public function getElement()
    {
        return 'package/block_type_set_item_list';
    }

}
