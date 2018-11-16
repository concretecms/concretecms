<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;

class Deactivation extends DashboardPageController
{

    public function update()
    {
        if ($this->token->validate("update")) {
            $post = $this->request->request;

            $config = $this->app->make('config');
            $enableAutomaticUserDeactivation = $this->request->request->get('enableAutomaticUserDeactivation') ?
                true : false;
            $config->save('concrete.user.deactivation.message', $this->request->request->get('inactiveMessage'));
            $config->save('concrete.user.deactivation.enable_login_threshold_deactivation',
                $enableAutomaticUserDeactivation);
            $config->save('concrete.user.deactivation.login.threshold',
                (int) $this->request->request->get('userDeactivationDays'));

            $enableLogoutDeactivation = (bool) $post->get('enableLogoutDeactivation', false);
            $userLoginAmount = max(0, (int) $post->get('userLoginAmount', 5));
            $userLoginDuration = max(0, (int) $post->get('userLoginDuration', 300));

            $config->save('concrete.user.deactivation.authentication_failure.enabled', $enableLogoutDeactivation);
            $config->save('concrete.user.deactivation.authentication_failure.amount', $userLoginAmount);
            $config->save('concrete.user.deactivation.authentication_failure.duration', $userLoginDuration);

            $this->flash('success', t('Deactivation settings saved successfully.'));
            $this->redirect('/dashboard/system/registration/deactivation');

        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }

    public function view()
    {
        $config = $this->app->make('config');
        $this->set('inactiveMessage', $config->get('concrete.user.deactivation.message'));
        $this->set('enableAutomaticUserDeactivation', (bool) $config->get('concrete.user.deactivation.enable_login_threshold_deactivation'));
        $this->set('userDeactivationDays', $config->get('concrete.user.deactivation.login.threshold'));

        $this->set('enableLogoutDeactivation', (bool) $config->get('concrete.user.deactivation.authentication_failure.enabled', false));
        $this->set('userLoginAmount', $config->get('concrete.user.deactivation.authentication_failure.amount'));
        $this->set('userLoginDuration', $config->get('concrete.user.deactivation.authentication_failure.duration'));
    }

}
