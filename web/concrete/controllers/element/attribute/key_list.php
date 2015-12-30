<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Controller\ElementController;

class KeyList extends ElementController
{

    protected $dashboard_page_path;
    protected $dashboard_page_add_action = 'select_type';
    protected $dashboard_page_edit_action = 'edit';
    protected $dashboard_page_parameters = array();

    protected $attributes = array();
    protected $types = array();

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getElement()
    {
        return 'attribute/key/list';
    }


    /**
     * @return array
     */
    public function getAttributeTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setAttributeTypes($types)
    {
        $this->types = $types;
    }

    public function getAddAttributeTypeURL(Type $type)
    {
        $args = array($this->getDashboardPagePath(), $this->getDashboardPageAddAction());
        $args = array_merge($args, $this->getDashboardPageParameters(), array($type->getAttributeTypeID()));
        return call_user_func_array(array('\URL', 'to'), $args);
    }

    public function getEditAttributeKeyURL(AttributeKeyInterface $key)
    {
        $args = array($this->getDashboardPagePath(), $this->getDashboardPageEditAction());
        $args = array_merge($args, $this->getDashboardPageParameters(), array($key->getAttributeKeyID()));
        return call_user_func_array(array('\URL', 'to'), $args);
    }


    public function view()
    {
        $types = array();
        foreach($this->getAttributeTypes() as $type) {
            $types[$type->getAttributeTypeID()] = $type;
        }
        $this->set('types', $types);
        $this->set('attributes', $this->getAttributes());
    }

    /**
     * @return mixed
     */
    public function getDashboardPagePath()
    {
        return $this->dashboard_page_path;
    }

    /**
     * @param mixed $dashboard_page_path
     */
    public function setDashboardPagePath($dashboard_page_path)
    {
        $this->dashboard_page_path = $dashboard_page_path;
    }

    /**
     * @return string
     */
    public function getDashboardPageAddAction()
    {
        return $this->dashboard_page_add_action;
    }

    /**
     * @param string $dashboard_page_add_action
     */
    public function setDashboardPageAddAction($dashboard_page_add_action)
    {
        $this->dashboard_page_add_action = $dashboard_page_add_action;
    }

    /**
     * @return array
     */
    public function getDashboardPageParameters()
    {
        return $this->dashboard_page_parameters;
    }

    /**
     * @param array $dashboard_page_parameters
     */
    public function setDashboardPageParameters($dashboard_page_parameters)
    {
        $this->dashboard_page_parameters = $dashboard_page_parameters;
    }

    /**
     * @return string
     */
    public function getDashboardPageEditAction()
    {
        return $this->dashboard_page_edit_action;
    }

    /**
     * @param string $dashboard_page_edit_action
     */
    public function setDashboardPageEditAction($dashboard_page_edit_action)
    {
        $this->dashboard_page_edit_action = $dashboard_page_edit_action;
    }


}
