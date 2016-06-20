<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\EditResponse;

class DeleteAlias extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/delete_alias';

    protected function canAccess()
    {
        return $this->permissions->canDeletePage() && $this->page->isAlias();
    }

    public function view()
    {
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $c = $this->page;
            $pr = new EditResponse();
            if ($c->isExternalLink()) {
                $pr->setMessage(t('External Link deleted.'));
            } else {
                $pr->setMessage(t("Alias deleted."));
            }
            $c->removeThisAlias();
            $pr->setPage($c);
            $pr->outputJSON();
        }
    }
}
