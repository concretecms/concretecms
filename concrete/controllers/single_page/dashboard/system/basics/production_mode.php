<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;

class ProductionMode extends DashboardPageController
{

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $config->save('concrete.security.production.mode', $this->request->get('production_mode'));
            $config->save(
                'concrete.security.production.staging.show_notification_to_unregistered_users',
                (bool)$this->request->get('show_notification_to_unregistered_users')
            );
            $this->flash('success', t('Production mode setting updated.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }

    public function view()
    {
        $config = $this->app->make('config');
        $this->set('production_mode', $config->get('concrete.security.production.mode'));
        $this->set(
            'show_notification_to_unregistered_users',
            (bool)$config->get('concrete.security.production.staging.show_notification_to_unregistered_users')
        );
    }
}
