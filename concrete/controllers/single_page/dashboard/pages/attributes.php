<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{
    protected function getCategoryObject()
    {
        return Category::getByHandle('collection');
    }

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
        $category = Category::getByHandle('collection');
        $this->executeUpdate($key,
            \URL::to('/dashboard/pages/attributes', 'view')
        );
    }

    public function select_type($type = null)
    {
        $type = Type::getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/pages/attributes', 'view', $id)
        );
    }

    public function add($type = null)
    {
        $this->select_type($type);
        $type = Type::getByID($type);
        $this->executeAdd($type, \URL::to('/dashboard/pages/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $key = CollectionKey::getByID($akID);
        $this->executeDelete($key,
            \URL::to('/dashboard/pages/attributes', 'view')
        );
    }
}
