<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Controller\ElementController;

class TypeList extends ElementController
{

    protected $base_url_callback;

    public function getElement()
    {
        return 'dashboard/attribute/type_list';
    }

    public function setBaseURL($args)
    {
        $this->base_url_callback = func_get_args();
    }

    public function getSelectTypeURL(Type $type)
    {
        $args = $this->base_url_callback;
        $args[] = $type->getAttributeTypeID();
        return call_user_func_array(array('\URL', 'to'), $args);
    }

    public function view()
    {
        $types = array();
        foreach(Type::getAttributeTypeList() as $type) {
            $types[$type->getAttributeTypeID()] = $type;
        }
        $this->set('types', $types);
    }

}
