<?php

namespace Concrete\Controller\Dialog\Area\Edit;

class AddFromStack extends \Concrete\Controller\Dialog\Area\Edit
{
    protected $viewPath = '/dialogs/area/edit/add_from_stack';

    public function view()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\Area\Edit::canAccess()
     */
    protected function canAccess()
    {
        return parent::canAccess() && $this->areaPermissions->canAddStacks();
    }
}
