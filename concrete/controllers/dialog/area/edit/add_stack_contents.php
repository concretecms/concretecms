<?php

namespace Concrete\Controller\Dialog\Area\Edit;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;

class AddStackContents extends \Concrete\Controller\Dialog\Area\Edit
{
    protected $viewPath = '/dialogs/area/edit/add_stack_contents';

    /**
     * @var \Concrete\Core\Page\Stack\Stack
     */
    protected $stack;

    /**
     * @var \Concrete\Core\Permission\Checker
     */
    protected $stackPermissions;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\Area\Edit::on_start()
     */
    public function on_start()
    {
        parent::on_start();
        $stackID = $this->request->get('stackID');
        $this->stack = $stackID ? Stack::getByID($stackID) : null;
        if (!$this->stack) {
            throw new UserMessageException('Invalid Stack');
        }
        $this->stackPermissions = new Checker($this->stack);
    }

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
        return parent::canAccess() && $this->stackPermissions->canRead() && $this->areaPermissions->canAddStacks();
    }
}
