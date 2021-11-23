<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Controller\Element\Attribute\SiteStandardListHeader;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\CategoryObjectInterface;
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

        $this->renderEdit($key, Url::to('/dashboard/system/basics/attributes', 'view'));
    }

    public function update($akID = null)
    {
        $this->edit($akID);

        $akc = $this->getCategoryObject()->getAttributeKeyCategory();
        $key = $akc->getAttributeKeyByID($akID);

        $this->executeUpdate($key, Url::to('/dashboard/system/basics/attributes', 'view'));
    }

    public function select_type($type = null)
    {
        $typeFactory = $this->app->make(TypeFactory::class);
        $type = $typeFactory->getByID($type);

        $this->renderAdd($type, Url::to('/dashboard/system/basics/attributes', 'view'));
    }

    public function add($type = null)
    {
        $this->select_type($type);
        $typeFactory = $this->app->make(TypeFactory::class);
        $type = $typeFactory->getByID($type);

        $this->executeAdd($type, Url::to('/dashboard/system/basics/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $akc = $this->getCategoryObject()->getAttributeKeyCategory();
        $key = $akc->getAttributeKeyByID($akID);

        $this->executeDelete($key, Url::to('/dashboard/system/basics/attributes', 'view'));
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

        return $categoryService->getByHandle('site');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\DashboardAttributesPageController::getHeaderMenu()
     */
    protected function getHeaderMenu(CategoryObjectInterface $category)
    {
        return new SiteStandardListHeader($category);
    }
}
