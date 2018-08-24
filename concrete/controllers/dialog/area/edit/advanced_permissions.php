<?php

namespace Concrete\Controller\Dialog\Area\Edit;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class AdvancedPermissions extends \Concrete\Controller\Dialog\Area\Edit
{
    protected $viewPath = '/dialogs/area/edit/advanced_permissions';

    /**
     * @var \Concrete\Core\Permission\Key\Key
     */
    protected $permissionKey;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\Area\Edit::on_start()
     */
    public function on_start()
    {
        parent::on_start();
        $pkID = $this->request->get('pkID');
        $this->permissionKey = $pkID ? PermissionKey::getByID($pkID) : null;
        if (!$this->permissionKey) {
            throw new UserMessageException('Invalid Permission Key');
        }
        $this->permissionKey->setPermissionObject($this->area);
        $this->set('pk', $this->permissionKey);
    }

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
