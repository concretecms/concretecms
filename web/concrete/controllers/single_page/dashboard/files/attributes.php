<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{
    protected function getCategoryEntityObject()
    {
        return Category::getByHandle('file');
    }

    public function view()
    {
        $this->renderList();
    }

    public function edit($akID = null)
    {
        $key = FileKey::getByID($akID);
        $this->renderEdit($key,
            \URL::to('/dashboard/files/attributes', 'view')
        );
    }

    public function update($akID = null)
    {
        $this->edit($akID);
        $key = FileKey::getByID($akID);
        $this->executeUpdate($key,
            \URL::to('/dashboard/files/attributes', 'view')
        );
    }

    public function select_type($type = null)
    {
        $type = Type::getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/files/attributes', 'view')
        );
    }

    public function add($type = null)
    {
        $this->select_type($type);
        $type = Type::getByID($type);
        $this->executeAdd($type, \URL::to('/dashboard/files/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $key = FileKey::getByID($akID);
        $this->executeDelete($key,
            \URL::to('/dashboard/files/attributes', 'view')
        );
    }
}
