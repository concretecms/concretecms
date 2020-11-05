<?php

namespace Concrete\Controller\Backend\Page\Type\Composer\Form;

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as ControlType;
use Concrete\Core\Page\Type\Composer\FormLayoutSet;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class AddControl extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = 'backend/page/type/composer/form/add_control';

    public function view(): ?Response
    {
        $this->checkAccess();
        $this->set('ui', $this->app->make(UserInterface::class));
        $this->set('types', ControlType::getList());
        $this->set('set', $this->getFormLayoutSet());

        return null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkAccess(): void
    {
        $c = Page::getByPath('/dashboard/pages/types/form');
        $cp = new Checker($c);
        if (!$cp->canViewPage()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    protected function getFormLayoutSetID(): ?int
    {
        $id = $this->request->request->get('ptComposerFormLayoutSetID', $this->request->query->get('ptComposerFormLayoutSetID'));

        return $this->app->make(Numbers::class)->integer($id) ? (int) $id : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFormLayoutSet(): FormLayoutSet
    {
        $id = $this->getFormLayoutSetID();
        $set = $id === null ? null : FormLayoutSet::getByID($id);

        if ($set === null) {
            throw new UserMessageException(t('Invalid set'));
        }

        return $set;
    }
}
