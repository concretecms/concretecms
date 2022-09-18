<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Login\PasswordUpgrade;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserList;

class GlobalPasswordReset extends DashboardPageController
{
    public function view()
    {
        $service = $this->app->make(PasswordUpgrade::class);
        $defaultResetMessage = $service->getDefaultPasswordResetMessage($service::PASSWORD_RESET_KEY, false);
        $resetMessage = $service->getPasswordResetMessage($service::PASSWORD_RESET_KEY, false);

        $this->set('resetText', $this->getResetText());
        $this->set('defaultResetMessage', $defaultResetMessage);
        $this->set('resetMessage', $resetMessage);

        $disableForm = $this->isFormDisabled();
        $this->set('disableForm', $disableForm);
        if ($disableForm) {
            $this->error->add(t('Only the Super User is allowed to reset all passwords.'));
        }
    }

    public function reset_passwords()
    {
        if (!$this->validateForm()) {
            return $this->view();
        }

        $service = $this->app->make(PasswordUpgrade::class);
        $service->setPasswordResetMessage($service::PASSWORD_RESET_KEY, (string) $this->request->request->get('resetMessage'));

        $this->resetUserPasswords();

        return $this->buildRedirect($this->app->make(ResolverManagerInterface::class)->resolve(['/login']));
    }

    protected function getResetText()
    {
        return tc(
            // i18n: a text to be asked to the users to confirm the global password reset operation
            'GlobalPasswordReset',
            'RESET'
        );
    }

    protected function validateForm(): bool
    {
        $post = $this->request->request;
        if (!$this->token->validate('global_password_reset_token')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (trim($post->get('resetMessage')) === '') {
            $this->error->add(t('The message can not be empty.'));
        }
        $resetText = $post->get('confirmation');
        if (mb_strtolower($resetText) !== mb_strtolower($this->getResetText())) {
            $this->error->add(t('You must type the reset phrase "%s" in the prompt to continue.', $this->getResetText()));
        }

        if ($this->isFormDisabled()) {
            $this->error->add(t('Only the Super User is allowed to reset all passwords.'));
        }

        return !$this->error->has();
    }

    protected function isFormDisabled(): bool
    {
        return !$this->app->make(User::class)->isSuperUser();
    }

    protected function resetUserPasswords(): void
    {
        $users = new UserList();
        $users->ignorePermissions();
        foreach ($users->getResults() as $userInfo) {
            if ($userInfo instanceof UserInfo) {
                $userInfo->markAsPasswordReset();
            }
        }
    }
}
