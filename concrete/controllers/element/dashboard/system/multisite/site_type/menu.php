<?php

namespace Concrete\Controller\Element\Dashboard\System\Multisite\SiteType;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Menu extends ElementController
{
    /**
     * @var \Concrete\Core\Entity\Site\Type
     */
    protected $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\ElementController::getElement()
     */
    public function getElement()
    {
        return 'dashboard/system/multisite/site_type/menu';
    }

    public function view()
    {
        $c = Page::getCurrentPage();
        switch ($c->getPageController()->getAction()) {
            case 'view_skeleton':
                $active = 'skeleton';
                break;
            case 'view_attributes':
                $active = 'attributes';
                break;
            case 'view_groups':
            case 'add_group':
                $active = 'groups';
                break;
            case 'view_type':
                $active = 'details';
                break;
            case 'edit':
            case 'update':
                $active = 'edit';
                break;
            default:
                $active = '';
                break;
        }
        $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
        $this->set('active', $active);
        $this->set('type', $this->type);
    }
}
