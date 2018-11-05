<?php

namespace Concrete\Controller\Dialog\Area\Edit;

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Permissions extends \Concrete\Controller\Dialog\Area\Edit
{
    protected $viewPath = '/dialogs/area/edit/permissions';

    public function view()
    {
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\Area\Edit::canAccess()
     */
    protected function canAccess()
    {
        return parent::canAccess() && $this->areaPermissions->canEditAreaPermissions();
    }
}
