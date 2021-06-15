<?php

namespace Concrete\Controller\Backend\Page\Type\Composer\Form\AddControl;

use Concrete\Controller\Backend\Page\Type\Composer\Form\AddControl;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as ControlType;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Pick extends AddControl
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\Page\Type\Composer\Form\AddControl::$viewPath
     */
    protected $viewPath = 'backend/page/type/composer/form/add_control/pick';

    public function view(): ?Response
    {
        $this->checkAccess();
        $set = $this->getFormLayoutSet();
        $control = $this->getControl();
        $this->set('control', $control->addToPageTypeComposerFormLayoutSet($set));

        return null;
    }

    protected function getControlTypeID(): ?int
    {
        $id = $this->request->request->get('ptComposerControlTypeID');

        return $this->app->make(Numbers::class)->integer($id) ? (int) $id : null;
    }

    protected function getControlIdentifier(): string
    {
        $id = $this->request->request->get('ptComposerControlIdentifier');

        return is_string($id) ? $id : '';
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getControlType(): ControlType
    {
        $id = $this->getControlTypeID();
        $type = $id === null ? null : ControlType::getByID($id);
        if ($type === null) {
            throw new UserMessageException(t('Invalid type'));
        }

        return $type;
    }

    protected function getControl(): Control
    {
        $id = $this->getControlIdentifier();
        $type = $this->getControlType();
        $control = $id === '' ? null : $type->getPageTypeComposerControlByIdentifier($id);
        if (!is_object($control)) {
            throw new UserMessageException(t('Invalid control'));
        }

        return $control;
    }
}
