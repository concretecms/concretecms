<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Controller\ElementController;

class KeyList extends ElementController
{
    protected $dashboard_page_path;
    protected $dashboard_page_add_action = 'select_type';
    protected $dashboard_page_edit_action = 'edit';
    protected $dashboard_page_sort_action = 'sort_attribute_set';
    protected $dashboard_page_parameters = array();
    protected $enable_sorting = true;
    protected $attribute_sets = array();
    protected $unassigned_attribute_keys = array();
    protected $category;

    protected $types = array();

    /**
     * @return bool
     */
    public function enableSorting()
    {
        return $this->enable_sorting;
    }

    /**
     * @param bool $enable_sorting
     */
    public function setEnableSorting($enable_sorting)
    {
        $this->enable_sorting = $enable_sorting;
    }

    /**
     * @return array
     */
    public function getAttributeSets()
    {
        return $this->attribute_sets;
    }

    /**
     * @param array $attribute_sets
     */
    public function setAttributeSets($attribute_sets)
    {
        $this->attribute_sets = $attribute_sets;
    }

    /**
     * @return array
     */
    public function getUnassignedAttributeKeys()
    {
        return $this->unassigned_attribute_keys;
    }

    /**
     * @param array $unassigned_attribute_keys
     */
    public function setUnassignedAttributeKeys($unassigned_attribute_keys)
    {
        $this->unassigned_attribute_keys = $unassigned_attribute_keys;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->category = $category;
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

    public function getSortAttributeCategoryURL()
    {
        $args = array($this->getDashboardPagePath(), $this->getDashboardPageSortAction());

        return call_user_func_array(array('\URL', 'to'), $args);
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
        foreach ($this->getAttributeTypes() as $type) {
            $types[$type->getAttributeTypeID()] = $type;
        }
        $this->set('types', $types);
        $this->set('enableSorting', $this->enableSorting());
        $this->set('sortable_sets', $this->getAttributeSets());
        $this->set('unassigned', $this->getUnassignedAttributeKeys());
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
    public function getDashboardPageSortAction()
    {
        return $this->dashboard_page_sort_action;
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
