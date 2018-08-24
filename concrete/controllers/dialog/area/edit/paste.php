<?php

namespace Concrete\Controller\Dialog\Area\Edit;

class Paste extends \Concrete\Controller\Dialog\Area\Edit
{
    protected $viewPath = '/dialogs/area/edit/paste';

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
        return parent::canAccess() && $this->areaPermissions->canAddBlocks();
    }
}
