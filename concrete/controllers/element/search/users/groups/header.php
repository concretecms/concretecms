<?php

namespace Concrete\Controller\Element\Search\Users\Groups;

use Concrete\Core\Controller\ElementController;

class Header extends ElementController
{

    protected $canAddGroup = false;

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

    public function view()
    {
        $this->set("canAddGroup", $this->canAddGroup);
    }

}
