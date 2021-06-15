<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Mail;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Mail\Service;
use Concrete\Core\Page\Controller\DashboardPageController;

class Logging extends DashboardPageController
{
    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $logModes = [
            Service::LOG_MAILS_NONE => t("Nothing"),
            Service::LOG_MAILS_ONLY_METADATA => t("Only Metadata"),
            Service::LOG_MAILS_METADATA_AND_BODY => t("Metadata and Body")
        ];

        if ($this->request->getMethod() === "POST") {
            if ($this->token->validate("change_mail_logging")) {
                $logMode = $this->request->request->get("logMode");

                if (in_array($logMode, array_keys($logModes))) {
                    $config->save("concrete.log.emails", $logMode);

                    $this->set('success', t("The mail logging settings were updated successfully."));
                } else {
                    $this->error->add(t("The log settings are invalid."));
                }
            } else {
                $this->error->add($this->token->getErrorMessage());
            }
        }

        $logMode = $config->get("concrete.log.emails", Service::LOG_MAILS_NONE);

        if ((is_numeric($logMode) && $logMode == 1) || $logMode === true) {
            $logMode = Service::LOG_MAILS_METADATA_AND_BODY; // legacy support
        }

        $this->set('logModes', $logModes);
        $this->set('logMode', $logMode);
    }
}
