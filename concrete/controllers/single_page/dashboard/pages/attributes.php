<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;
use Concrete\Core\Support\Facade\Url;

class Attributes extends DashboardAttributesPageController
{
    public function view()
    {
        $this->renderList();
    }

    public function edit($akID = null)
    {
        $akc = $this->getCategoryObject()->getAttributeKeyCategory();
        $key = $akc->getAttributeKeyByID($akID);

        $this->renderEdit($key, Url::to('/dashboard/pages/attributes', 'view'));
    }

    public function update($akID = null)
    {
        $this->edit($akID);

        $akc = $this->getCategoryObject()->getAttributeKeyCategory();
        $key = $akc->getAttributeKeyByID($akID);

        $this->executeUpdate($key, Url::to('/dashboard/pages/attributes', 'view'));
    }

    public function select_type($type = null)
    {
        $typeFactory = $this->app->make(TypeFactory::class);
        $type = $typeFactory->getByID($type);

        $this->renderAdd($type, Url::to('/dashboard/pages/attributes', 'view'));
    }

    public function add($type = null)
    {
        $this->select_type($type);
        $typeFactory = $this->app->make(TypeFactory::class);
        $type = $typeFactory->getByID($type);

        $this->executeAdd($type, Url::to('/dashboard/pages/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $akc = $this->getCategoryObject()->getAttributeKeyCategory();
        $key = $akc->getAttributeKeyByID($akID);

        $this->executeDelete($key, Url::to('/dashboard/pages/attributes', 'view'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\DashboardAttributesPageController::getCategoryObject()
     *
     * @return \Concrete\Core\Entity\Attribute\Category
     */
    protected function getCategoryObject()
    {
        $categoryService = $this->app->make(CategoryService::class);

        return $categoryService->getByHandle('collection');
    }
}
