<?php

namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Site\InstallationService;

/**
 * Adds multisite checking to a Dashboard page controller. Note: This must be used from within a Dashboard
 * page controller.
 */
trait MultisiteRequiredTrait
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
     */
    public function on_start()
    {
        parent::on_start();
        $service = $this->app->make(InstallationService::class);
        if (!$service->isMultisiteEnabled()) {
            return $this->buildRedirect('/dashboard/system/multisite/settings', 'multisite_required');
        }

        return null;
    }
}
