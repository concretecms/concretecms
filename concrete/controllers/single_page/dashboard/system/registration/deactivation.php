<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Utility\Service\Validation\Numbers;

class Deactivation extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('inactiveMessage', (string) $config->get('concrete.user.deactivation.message'));
        $this->set('enableAutomaticUserDeactivation', (bool) $config->get('concrete.user.deactivation.enable_login_threshold_deactivation'));
        $this->set('userDeactivationDays', (int) $config->get('concrete.user.deactivation.login.threshold'));
        $this->set('enableLogoutDeactivation', (bool) $config->get('concrete.user.deactivation.authentication_failure.enabled', false));
        $this->set('userLoginAmount', (int) $config->get('concrete.user.deactivation.authentication_failure.amount'));
        $this->set('userLoginDuration', (int) $config->get('concrete.user.deactivation.authentication_failure.duration'));
    }

    public function update()
    {
        $post = $this->request->request;
        if (!$this->token->validate('update')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $valn = $this->app->make(Numbers::class);
        $enableAutomaticUserDeactivation = (bool) $post->get('enableAutomaticUserDeactivation');
        if ($enableAutomaticUserDeactivation) {
            $userDeactivationDays = $post->get('userDeactivationDays');
            if ($valn->integer($userDeactivationDays, 1)) {
                $userDeactivationDays = (int) $userDeactivationDays;
            } else {
                $this->error->add(t('Please specify a positive integer for the number of days.'));
            }
        }
        $enableLogoutDeactivation = (bool) $post->get('enableLogoutDeactivation');
        if ($enableLogoutDeactivation) {
            $userLoginAmount = $post->get('userLoginAmount');
            if ($valn->integer($userLoginAmount, 1)) {
                $userLoginAmount = (int) $userLoginAmount;
            } else {
                $this->error->add(t('Please specify a positive integer for the number of failed logins.'));
            }
            $userLoginDuration = $post->get('userLoginDuration');
            if ($valn->integer($userLoginDuration, 1)) {
                $userLoginDuration = (int) $userLoginDuration;
            } else {
                $this->error->add(t('Please specify a positive integer for the number of days.'));
            }
        }
        if ($this->error->has()) {
            return $this->view();
        }

        $config = $this->app->make('config');
        $config->save('concrete.user.deactivation.message', (string) $post->get('inactiveMessage'));
        $config->save('concrete.user.deactivation.enable_login_threshold_deactivation', $enableAutomaticUserDeactivation);
        if ($enableAutomaticUserDeactivation) {
            $config->save('concrete.user.deactivation.login.threshold', $userDeactivationDays);
        }
        $config->save('concrete.user.deactivation.authentication_failure.enabled', $enableLogoutDeactivation);
        if ($enableLogoutDeactivation) {
            $config->save('concrete.user.deactivation.authentication_failure.amount', $userLoginAmount);
            $config->save('concrete.user.deactivation.authentication_failure.duration', $userLoginDuration);
        }

        $this->flash('success', t('Deactivation settings saved successfully.'));

        return $this->buildRedirect($this->action());
    }
}
