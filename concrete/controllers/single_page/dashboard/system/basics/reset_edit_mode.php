<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Doctrine\DBAL\DBALException;

class ResetEditMode extends DashboardSitePageController
{

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            try {

                /** @var Connection $db */
                $db = $this->app->make(Connection::class);
                $db->executeQuery("TRUNCATE Piles");
                $db->executeQuery("TRUNCATE PileContents");

            } catch (DBALException $exception) {
                $this->error->add(t("Error while clearing the clipboard."));
            }

            Page::forceCheckInForAllPages();

            if (!$this->error->has()) {
                $this->flash('success', t('Clipboard and Edit Mode successfully reset on all pages.'));
            }

            $this->redirect('/dashboard/system/basics/reset_edit_mode');
        }
    }
}
