<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Controller\ElementController;

class Form extends ElementController
{
    protected $back_button_url;
    protected $type;
    protected $dashboard_page_submit_action = 'add';
    protected $dashboard_page_parameters = array();
    protected $category;

    public function __construct(Type $type)
    {
        $this->type = $type;
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getBackButtonUrl()
    {
        return $this->back_button_url;
    }

    /**
     * @param mixed $back_button_url
     */
    public function setBackButtonUrl($back_button_url)
    {
        $this->back_button_url = $back_button_url;
    }

    public function getElement()
    {
        return 'attribute/key/form';
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
    public function setCategory($category)
    {
        $this->category = $category;
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
    public function getDashboardPageSubmitAction()
    {
        return $this->dashboard_page_submit_action;
    }

    /**
     * @param string $dashboard_page_submit_action
     */
    public function setDashboardPageSubmitAction($dashboard_page_submit_action)
    {
        $this->dashboard_page_submit_action = $dashboard_page_submit_action;
    }

    public function view()
    {
        $category = $this->category->getAttributeKeyCategory();
        $this->set('category', $category);
        $this->set('type', $this->type);
        $this->set('back', $this->getBackButtonUrl());
        $this->set('action', $this->getViewObject()->action($this->getDashboardPageSubmitAction(),
            $this->getDashboardPageParameters()
        ));
    }
}
