<?php

namespace Concrete\Controller\Backend\Page\Type\Composer\Form;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class EditControl extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = 'backend/page/type/composer/form/edit_control';

    public function view(): ?Response
    {
        $this->checkAccess();
        $setControl = $this->getSetControl();
        $control = $this->getControl($setControl);
        $templates = $this->getTemplates($control);
        $this->set('setControl', $setControl);
        $this->set('control', $control);
        $this->set('templates', $templates);
        $this->set('form', $this->app->make(Form::class));
        $this->set('valt', $this->app->make(Token::class));

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

    protected function getSetControlID(): ?int
    {
        $id = $this->request->request->get('ptComposerFormLayoutSetControlID', $this->request->query->get('ptComposerFormLayoutSetControlID'));

        return $this->app->make(Numbers::class)->integer($id) ? (int) $id : null;
    }

    protected function getSetControl(): FormLayoutSetControl
    {
        $id = $this->getSetControlID();
        $setControl = $id === null ? null : FormLayoutSetControl::getByID($id);
        if ($setControl === null) {
            throw new UserMessageException(t('Invalid control'));
        }

        return $setControl;
    }

    protected function getControl(FormLayoutSetControl $setControl): Control
    {
        return $setControl->getPageTypeComposerControlObject();
    }

    protected function getTemplates(Control $control): array
    {
        $customTemplates = $control->getPageTypeComposerControlCustomTemplates();
        $templates = ['' => t('** None')];
        foreach ($customTemplates as $template) {
            $templates[(string) $template->getPageTypeComposerControlCustomTemplateFilename()] = $template->getPageTypeComposerControlCustomTemplateName();
        }

        return $templates;
    }
}
