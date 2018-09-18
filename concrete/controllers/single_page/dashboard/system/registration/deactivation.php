<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;

class Deactivation extends DashboardPageController
{

    public function update()
    {
        if ($this->token->validate("update")) {
            $config = $this->app->make('config');
            $enableAutomaticUserDeactivation = $this->request->request->get('enableAutomaticUserDeactivation') ?
                true : false;
            $config->save('concrete.user.deactivation.message', $this->request->request->get('inactiveMessage'));
            $config->save('concrete.user.deactivation.enable_login_threshold_deactivation',
                $enableAutomaticUserDeactivation);
            $config->save('concrete.user.deactivation.login.threshold',
                (int) $this->request->request->get('userDeactivationDays'));

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
        $this->set('enableAutomaticUserDeactivation', !!$config->get('concrete.user.deactivation.enable_login_threshold_deactivation'));
        $this->set('userDeactivationDays', $config->get('concrete.user.deactivation.login.threshold'));
    }

}
