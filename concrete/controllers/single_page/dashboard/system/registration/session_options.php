<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class SessionOptions extends DashboardSitePageController
{
    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        /** @var Validation $validation */
        $validation = $this->app->make(Validation::class);

        if ($this->request->getMethod() === "POST") {
            $validation->setData($this->request->request->all());
            $validation->addRequiredToken("update_cookie_options");

            if ($validation->test()) {
                $config->save("concrete.session.cookie.cookie_secure", $this->request->request->has("secure"));
                $config->save("concrete.session.cookie.cookie_httponly", $this->request->request->has("httponly"));
                $config->save("concrete.session.cookie.cookie_raw", $this->request->request->has("raw"));
                $config->save("concrete.session.cookie.cookie_domain", strlen($this->request->request->get("domain")) > 0 ? $this->request->request->get("domain") : false);
                $config->save("concrete.session.cookie.cookie_samesite", strlen($this->request->request->get("samesite")) > 0 ? $this->request->request->get("samesite") : null);

                $this->set("success", t("The settings has been successfully updated."));
            } else {
                $this->error = $validation->getError();
            }
        }

        $this->set("secure", (bool)$config->get("concrete.session.cookie.cookie_secure"));
        $this->set("httponly", (bool)$config->get("concrete.session.cookie.cookie_httponly"));
        $this->set("raw", (bool)$config->get("concrete.session.cookie.cookie_raw"));
        $this->set("domain", (string)$config->get("concrete.session.cookie.cookie_domain"));
        $this->set("samesite", (string)$config->get("concrete.session.cookie.cookie_samesite"));
    }
}