<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{
    public function view()
    {
        $this->renderList();
    }

    public function edit($akID = null)
    {
        $key = CollectionKey::getByID($akID);
        $this->renderEdit($key,
            \URL::to('/dashboard/pages/attributes', 'view')
        );
    }

    public function update($akID = null)
    {
        $this->edit($akID);
        $key = CollectionKey::getByID($akID);
        $this->executeUpdate($key,
            \URL::to('/dashboard/pages/attributes', 'view')
        );
    }

    public function select_type($type = null)
    {
        $typeFactory = $this->app->make(TypeFactory::class);
        $type = $typeFactory->getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/pages/attributes', 'view')
        );
    }

    public function add($type = null)
    {
        $typeFactory = $this->app->make(TypeFactory::class);
        $this->select_type($type);
        $type = $typeFactory->getByID($type);
        $this->executeAdd($type, \URL::to('/dashboard/pages/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $key = CollectionKey::getByID($akID);
        $this->executeDelete($key,
            \URL::to('/dashboard/pages/attributes', 'view')
        );
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
        return Category::getByHandle('collection');
    }
}
