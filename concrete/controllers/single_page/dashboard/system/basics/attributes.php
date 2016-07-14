<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Controller\Element\Attribute\SiteStandardListHeader;
use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{
    protected function getCategoryObject()
    {
        return Category::getByHandle('site');
    }

    protected function getHeaderMenu(CategoryObjectInterface $category)
    {
        return new SiteStandardListHeader($category);
    }

    public function view()
    {
        $this->renderList();
    }

    public function edit($akID = null)
    {
        $key = SiteKey::getByID($akID);
        $this->renderEdit($key,
            \URL::to('/dashboard/system/basics/attributes', 'view')
        );
    }

    public function update($akID = null)
    {
        $this->edit($akID);
        $key = SiteKey::getByID($akID);
        $this->executeUpdate($key,
            \URL::to('/dashboard/system/basics/attributes', 'view')
        );
    }

    public function select_type($type = null)
    {
        $type = Type::getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/system/basics/attributes', 'view')
        );
    }

    public function add($type = null)
    {
        $this->select_type($type);
        $type = Type::getByID($type);
        $this->executeAdd($type, \URL::to('/dashboard/system/basics/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $key = SiteKey::getByID($akID);
        $this->executeDelete($key,
            \URL::to('/dashboard/system/basics/attributes', 'view')
        );
    }
}
