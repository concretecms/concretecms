<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Attribute\Category\SiteCategory;
use Concrete\Core\Attribute\Context\DashboardFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Marketplace\Update\Command\UpdateRemoteDataCommand;
use Concrete\Core\Marketplace\Update\UpdatedField;
use Concrete\Core\Marketplace\Update\UpdatedFieldInterface;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Site\Service;

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
        /**
         * @var Category $category
         * @var SiteCategory $controller
         */
        $category = AttributeKeyCategory::getByHandle('site');
        $controller = $category->getController();
        $sets = $controller->getSetManager()->getAttributeSets();
        $unassignedAttributes = $controller->getSetManager()->getUnassignedAttributeKeys();

        $this->set('sets', $sets);
        $this->set('unassignedAttributes', $unassignedAttributes);
        $this->set('totalAttributes', count(SiteKey::getList()));
        $this->set('site', $this->getSite());
        $this->set('renderer', new Renderer(new DashboardFormContext(), $this->getSite()));
    }

    public function sitename_saved()
    {
        $this->set('success', t("Your site's name and attributes have been saved."));
        $this->view();
    }

    public function update_sitename()
    {
        if ($this->token->validate('update_sitename')) {
            if ($this->isPost()) {
                $name = $this->request->request->get('SITE');
                $this->site->setSiteName($name);
                $this->entityManager->persist($this->site);
                $this->entityManager->flush();

                if ($this->site->isDefault()) {
                    $repository = $this->app->make(PackageRepositoryInterface::class);
                    if ($repository->getConnection()) {
                        $command = new UpdateRemoteDataCommand([new UpdatedField(UpdatedFieldInterface::FIELD_NAME, $name)]);
                        $this->app->executeCommand($command);
                    }
                }

                $attributes = SiteKey::getList();
                foreach ($attributes as $ak) {
                    $controller = $ak->getController();
                    $controller->setAttributeObject($this->site);
                    $value = $controller->createAttributeValueFromRequest();
                    $this->site->setAttribute($ak, $value);
                }

                $this->redirect('/dashboard/system/basics/name', 'sitename_saved');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }
}
