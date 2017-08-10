<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserList;

class GlobalPasswordReset extends DashboardPageController
{
    const PASSWORD_RESET_MESSAGE_KEY = 'concrete.password.reset.message';

    public function reset_passwords()
    {
        if (!$this->validateForm()) {
            return;
        }

        \Core::make('config/database')->save(self::PASSWORD_RESET_MESSAGE_KEY, $this->post('resetMessage'));

        $users = new UserList();
        $users->ignorePermissions();
        foreach ($users->getResults() as $userInfo) {
            if ($userInfo instanceof UserInfo) {
                $userInfo->resetUserPassword();
                $userInfo->markAsPasswordReset();
            }
        }

        $this->redirect('/');
    }

    private function validateForm()
    {
        $token = \Core::make('token');

        if (!$token->validate('global_password_reset_token')) {
            $this->error->add('Invalid Token.');
        }

        if (!$this->post('resetMessage')) {
            $this->error->add('Message can not be empty.');
        }

        $user = new User();
        if (!$user->isSuperUser()) {
            $this->error->add('Only the Super User is allowed to reset all passwords.');
        }

        return $this->error->has() ? false : true;
    }

    public function view()
    {
        $defaultMessage = t('Your user account is being upgraded and requires a new password. Please enter your email address below to create this now.');
        $resetMessage = \Core::make('config/database')->get(self::PASSWORD_RESET_MESSAGE_KEY, $defaultMessage);

        $this->set('resetMessage', $resetMessage);

        $user = new User();
        $this->set('disableForm', !$user->isSuperUser());
    }
}
