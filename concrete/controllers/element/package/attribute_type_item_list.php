<?php
namespace Concrete\Controller\Element\Package;

use Concrete\Core\Attribute\Key\Category;

class AttributeTypeItemList extends ItemList
{

    public function view()
    {
        parent::view();
        $this->set('text', \Core::make('helper/text'));
        $this->set('categories', Category::getList());
    }

    public function getElement()
    {
        return 'package/attribute_type_item_list';
    }

}
