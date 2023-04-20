<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Api;

use Concrete\Core\Api\Command\SynchronizeScopesCommand;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Page\Controller\DashboardPageController;

class Scopes extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make("config");
        $enable_api = (bool)$config->get('concrete.api.enabled');
        if (!$enable_api) {
            return $this->buildRedirect(['/dashboard/system/api/settings']);
        }

        $scopes = $this->entityManager->getRepository(Scope::class)
            ->findAll();
        $this->set('scopes', $scopes);
    }

    public function synchronize()
    {
        if (!$this->token->validate('synchronize')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $command = new SynchronizeScopesCommand();
            $this->app->executeCommand($command);
            $this->flash('success', t('Scopes rebuilt from API sources.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }

}
