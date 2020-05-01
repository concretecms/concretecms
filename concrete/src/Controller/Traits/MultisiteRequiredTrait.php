<?php
namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Site\InstallationService;

/**
 * Adds multisite checking to a Dashboard page controller. Note: This must be used from within a Dashboard
 * page controller.
 */
trait MultisiteRequiredTrait
{

    public function on_start()
    {
        parent::on_start();
        $service = $this->app->make(InstallationService::class);
        if (!$service->isMultisiteEnabled()) {
            $this->redirect('/dashboard/system/multisite/settings', 'multisite_required');
            exit;
        }
    }


}