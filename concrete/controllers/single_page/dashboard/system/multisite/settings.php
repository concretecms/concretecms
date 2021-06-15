<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Multisite;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Site\InstallationService;

class Settings extends DashboardPageController
{
    public function view()
    {
        $this->set('service', $this->app->make(InstallationService::class));
    }

    public function multisite_required()
    {
        return $this->view();
    }

    public function enable_multisite()
    {
        if (!$this->token->validate('enable_multisite')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $service = $this->app->make(InstallationService::class);
        $error = $service->validateEnvironment();
        if ($error->has()) {
            $this->error->add($error);
        }

        if (!$this->error->has()) {
            $service->enableMultisite();
            $this->flash('success', t('Multiple sites enabled.'));

            return $this->buildRedirect($this->action());
        }
        return $this->view();
    }
}
