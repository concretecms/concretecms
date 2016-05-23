<?php
namespace Concrete\Controller\Element\Package;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\ItemInterface;

class BlockTypeItemList extends ItemList
{

    public function view()
    {
        parent::view();
        $this->set('ci', \Core::make('helper/concrete/urls'));
    }

    public function getElement()
    {
        return 'package/block_type_item_list';
    }

}
