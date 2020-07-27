<?php
namespace Concrete\Controller\Element\Search\Users\Groups;

use Concrete\Core\Controller\ElementController;
use Concrete\Controller\Search\Groups;

class Header extends ElementController
{

    protected $canAddGroup = false;
    /** @var Groups */
    protected $searchController;
    /**
     * @return bool
     */
    public function isCanAddGroup(): bool
    {
        return $this->canAddGroup;
    }

    /**
     * @param bool $canAddGroup
     * @return Header
     */
    public function setCanAddGroup(bool $canAddGroup): Header
    {
        $this->canAddGroup = $canAddGroup;
        return $this;
    }

    public function getElement()
    {
        return 'users/groups/search_header';
    }

    /**
     * @return Groups
     */
    public function getSearchController(): Groups
    {
        return $this->searchController;
    }

    /**
     * @param Groups $searchController
     * @return Header
     */
    public function setSearchController(Groups $searchController): Header
    {
        $this->searchController = $searchController;
        return $this;
    }

    public function view() {
        $this->set("canAddGroup", $this->canAddGroup);
        $this->set("searchController", $this->searchController);
    }

}
