<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Attribute\Context\DashboardFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Site\Service;
use Config;

class Name extends DashboardSitePageController
{

    protected $service;

    public function __construct(\Concrete\Core\Page\Page $c, Service $service)
    {
        $this->service = $service;
        parent::__construct($c);
    }

    public function view()
    {
        $attributes = SiteKey::getList();
        $this->set('attributes', $attributes);
        $this->set('site', $this->getSite());
        $this->set('renderer', new Renderer(new DashboardFormContext(), $this->getSite()));
    }

    public function sitename_saved()
    {
        $this->set('message', t("Your site's name has been saved."));
        $this->view();
    }

    public function update_sitename()
    {
        if ($this->token->validate("update_sitename")) {
            if ($this->isPost()) {
                $this->site->setSiteName($this->request->request->get('SITE'));
                $this->entityManager->persist($this->site);
                $this->entityManager->flush();

                $attributes = SiteKey::getList();
                foreach ($attributes as $ak) {
                    $controller = $ak->getController();
                    $value = $controller->createAttributeValueFromRequest();
                    $this->site->setAttribute($ak, $value);
                }

                $this->redirect('/dashboard/system/basics/name', 'sitename_saved');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
        $this->view();
    }
}
