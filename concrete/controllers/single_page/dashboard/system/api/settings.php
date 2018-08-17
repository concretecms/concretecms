<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Api;

use Concrete\Core\Page\Controller\DashboardPageController;

class Settings extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make("config");
        $this->set("enable_api", $config->get('concrete.api.enabled'));
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $enable_api = $this->request->request->get("enable_api") ? true : false;
            $this->app->make('config')->save('concrete.api.enabled', $enable_api);
            $this->flash('success', t("API Settings updated successfully."));
            return $this->redirect('/dashboard/system/api/settings');
        }
    }

}
