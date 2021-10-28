<?php

namespace Concrete\Controller\Backend\Page\Type\Composer\Form\EditControl;

use Concrete\Controller\Backend\Page\Type\Composer\Form\EditControl;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Validation\SanitizeService;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Save extends EditControl
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = 'backend/page/type/composer/form/edit_control/save';

    public function view(): ?Response
    {
        $this->checkAccess();
        $this->checkCSRF();
        $setControl = $this->getSetControl();
        $this->updateSetControl($setControl);
        $this->set('setControl', $setControl);

        return null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkCSRF(): void
    {
        $token = $this->app->make(Token::class);
        if (!$token->validate('update_set_control')) {
            throw new UserMessageException($token->getErrorMessage());
        }
    }

    protected function updateSetControl(FormLayoutSetControl $setControl): void
    {
        $setControl->updateFormLayoutSetControlCustomLabel($this->getPostedLabel());
        $setControl->updateFormLayoutSetControlCustomTemplate($this->getPostedTemplate());
        $setControl->updateFormLayoutSetControlDescription($this->getPostedDescription());
        $control = $this->getControl($setControl);
        if ($control->pageTypeComposerFormControlSupportsValidation()) {
            $setControl->updateFormLayoutSetControlRequired($this->getPostedRequired());
        }
    }

    protected function getPostedLabel(): string
    {
        $label = $this->request->request->get('ptComposerFormLayoutSetControlCustomLabel');

        return is_string($label) ? $this->app->make(SanitizeService::class)->sanitizeString($label) : '';
    }

    protected function getPostedTemplate(): string
    {
        $template = $this->request->request->get('ptComposerFormLayoutSetControlCustomTemplate');

        return is_string($template) ? $this->app->make(SanitizeService::class)->sanitizeString($template) : '';
    }

    protected function getPostedDescription(): string
    {
        $description = $this->request->request->get('ptComposerFormLayoutSetControlDescription');

        return is_string($description) ? $this->app->make(SanitizeService::class)->sanitizeString($description) : '';
    }

    protected function getPostedRequired(): bool
    {
        return !empty($this->request->request->get('ptComposerFormLayoutSetControlRequired'));
    }
}
