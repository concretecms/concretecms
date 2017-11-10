<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;
use Concrete\Core\Attribute\Key\EventKey;

class Attributes extends DashboardAttributesPageController
{
    protected function getCategoryObject()
    {
        return Category::getByHandle('event');
    }

    public function view()
    {
        $this->renderList();
    }

    public function edit($akID = null)
    {
        $key = EventKey::getByID($akID);
        $this->renderEdit($key,
            \URL::to('/dashboard/calendar/attributes', 'view')
        );
    }

    public function update($akID = null)
    {
        $this->edit($akID);
        $key = EventKey::getByID($akID);
        $this->executeUpdate($key,
            \URL::to('/dashboard/calendar/attributes', 'view')
        );
    }

    public function select_type($type = null)
    {
        $type = Type::getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/calendar/attributes', 'view')
        );
    }

    public function add($type = null)
    {
        $this->select_type($type);
        $type = Type::getByID($type);
        $this->executeAdd($type, \URL::to('/dashboard/calendar/attributes', 'view'));
    }

    public function delete($akID = null)
    {
        $key = EventKey::getByID($akID);
        $this->executeDelete($key,
            \URL::to('/dashboard/calendar/attributes', 'view')
        );
    }
}
