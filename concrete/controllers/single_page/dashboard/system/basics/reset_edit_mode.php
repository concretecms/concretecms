<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;

class ResetEditMode extends DashboardSitePageController
{

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            Page::forceCheckInForAllPages();
            $this->flash('success', t('Edit Mode successfully reset on all pages.'));
            $this->redirect('/dashboard/system/basics/reset_edit_mode');
        }
    }
}
