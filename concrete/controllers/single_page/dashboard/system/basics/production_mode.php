<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Command\Task\Traits\DashboardTaskRunnerTrait;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Production\Modes;

class ProductionMode extends DashboardPageController
{

    use DashboardTaskRunnerTrait;

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

    public function enable_production_mode()
    {
        if (!$this->token->validate('enable_production_mode')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $config->save('concrete.security.production.mode', Modes::MODE_PRODUCTION);
            if ($this->request->request->get('action') === 'run_tests') {

                $task = $this->app->make(TaskService::class)->getByHandle('production_status');
                $this->executeTask($task);

                $this->flash('success', t('Site production mode set to production, tests started.'));
                return $this->buildRedirect(['/dashboard/welcome/health']);


            } else {
                $this->flash('success', t('Site production mode set to production. Tests were skipped.'));
                return $this->buildRedirect($this->action('view'));
            }
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
