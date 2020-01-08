<?php
namespace Concrete\Controller\Element\Dashboard\SiteType;

use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Controller\ElementController;

class Menu extends ElementController
{

    protected $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
        parent::__construct();
    }

    public function getElement()
    {
        return 'dashboard/system/site_types/menu';
    }

    public function view()
    {
        $c = \Page::getCurrentPage();
        $active = '';
        $controller = $c->getPageController();
        if ($controller->getTask() == 'view_skeleton') {
            $active = 'skeleton';
        } else if ($controller->getTask() == 'view_attributes') {
            $active = 'attributes';
        } else if ($controller->getTask() == 'view_groups' || $controller->getTask() == 'add_group' ||
            $controller->getTask() == 'edit_group') {
            $active = 'groups';
        } else if ($controller->getTask() == 'view_type') {
            $active = 'details';
        } else if ($controller->getTask() == 'edit' || $controller->getTask() == 'update') {
            $active = 'edit';
        }
        $this->set('active', $active);
        $this->set('type', $this->type);
    }


}
