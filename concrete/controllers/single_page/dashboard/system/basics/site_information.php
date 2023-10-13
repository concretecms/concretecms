<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\SiteInformation\SiteInformationSurvey;
use Concrete\Core\User\User;

class SiteInformation extends DashboardPageController
{

    public function submit()
    {
        $u = $this->app->make(User::class);
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$u->isSuperUser()) {
            $this->error->add(t('Only the super user may modify site information.'));
        }
        if (!$this->error->has()) {
            $survey = $this->app->make(SiteInformationSurvey::class);
            $survey->getSaver()->saveFromRequest($this->request);
            $this->flash('success', t('Site information updated.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }

    public function view()
    {
        $this->set('survey', $this->app->make(SiteInformationSurvey::class));
    }
}
