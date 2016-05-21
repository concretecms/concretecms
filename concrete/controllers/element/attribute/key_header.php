<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Controller\ElementController;

class KeyHeader extends ElementController
{
    protected $key;
    protected $dashboard_page_delete_action = 'delete';
    protected $dashboard_page_parameters;

    public function __construct(AttributeKeyInterface $key)
    {
        $this->key = $key;
        parent::__construct();
    }

    public function setDashboardPageDeleteAction($action)
    {
        $this->delete_action = $action;
    }

    /**
     * @return mixed
     */
    public function getDashboardPageDeleteAction()
    {
        return $this->dashboard_page_delete_action;
    }

    /**
     * @return mixed
     */
    public function getDashboardPageParameters()
    {
        return $this->dashboard_page_parameters;
    }

    /**
     * @param mixed $dashboard_page_parameters
     */
    public function setDashboardPageParameters($dashboard_page_parameters)
    {
        $this->dashboard_page_parameters = $dashboard_page_parameters;
    }

    public function getElement()
    {
        return 'attribute/key/header';
    }

    public function view()
    {
        $this->set('deleteAction', $this->getViewObject()->action($this->getDashboardPageDeleteAction(),
            $this->getDashboardPageParameters()
        ));
        $this->set('key', $this->key);
    }
}
