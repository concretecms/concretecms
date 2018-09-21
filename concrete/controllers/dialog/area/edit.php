<?php

namespace Concrete\Controller\Dialog\Area;

use Concrete\Controller\Backend\UserInterface\Page as BackendPageController;
use Concrete\Core\Area\Area;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;

abstract class Edit extends BackendPageController
{
    /**
     * @var \Concrete\Core\Area\Area
     */
    protected $area;

    /**
     * @var \Concrete\Core\Permission\Checker
     */
    protected $areaPermissions;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface\Page::on_start()
     */
    public function on_start()
    {
        parent::on_start();
        $arHandle = (string) $this->request->query->get('arHandle', '');
        $this->area = $arHandle === '' ? null : Area::get($this->page, $arHandle);
        if (!$this->area) {
            throw new UserMessageException('Invalid Area');
        }
        if ($this->area->isGlobalArea()) {
            $cx = Stack::getByName($this->area->getAreaHandle());
            if (!$cx) {
                throw new UserMessageException('Invalid Stack');
            }
            $this->permissions = new Checker($cx);
            $ax = Area::get($cx, STACKS_AREA_NAME);
            if (!$this->area) {
                throw new UserMessageException('Invalid Area for Permissions');
            }
            $this->areaPermissions = new Checker($ax);
        } else {
            $this->areaPermissions = new Checker($this->area);
        }
        $this->set('a', $this->area);
        $this->set('ap', $this->areaPermissions);
        $this->set('token', $this->app->make('token'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }
}
