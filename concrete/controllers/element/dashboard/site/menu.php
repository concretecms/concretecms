<?php
namespace Concrete\Controller\Element\Dashboard\Site;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Controller\ElementController;

class Menu extends ElementController
{

    protected $type;

    public function __construct(Site $site)
    {
        $this->site = $site;
        parent::__construct();
    }

    public function getElement()
    {
        return 'dashboard/system/sites/menu';
    }

    public function view()
    {
        $c = \Page::getCurrentPage();
        $active = '';
        $controller = $c->getPageController();
        if ($controller->getTask() == 'view_domains') {
            $active = 'domains';
        } else {
            $active = 'details';
        }
        $this->set('active', $active);
        $this->set('site', $this->site);
    }


}
