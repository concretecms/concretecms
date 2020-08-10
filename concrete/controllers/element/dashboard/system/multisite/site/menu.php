<?php

namespace Concrete\Controller\Element\Dashboard\System\Multisite\Site;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Menu extends ElementController
{
    /**
     * @var \Concrete\Core\Entity\Site\Site
     */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\ElementController::getElement()
     */
    public function getElement()
    {
        return 'dashboard/system/multisite/site/menu';
    }

    public function view()
    {
        $c = Page::getCurrentPage();
        switch ($c->getPageController()->getAction()) {
            case 'view_domains':
                $active = 'domains';
                break;
            default:
                $active = 'details';
                break;
        }
        $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
        $this->set('active', $active);
        $this->set('site', $this->site);
    }
}
