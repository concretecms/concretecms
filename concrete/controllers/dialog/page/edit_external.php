<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\EditResponse;

class EditExternal extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/add_external';

    protected function canAccess()
    {
        return $this->permissions->canDeletePage() && $this->page->isExternalLink();
    }

    public function view()
    {
        $this->set('name', $this->page->getCollectionName());
        $this->set('link', $this->page->getCollectionPointerExternalLink());
        $this->set('openInNewWindow', $this->page->openCollectionPointerExternalLinkInNewWindow());
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $c = $this->page;
            $request = \Request::getInstance();
            $this->page->updateCollectionAliasExternal(
                $request->request->get('name'),
                $request->request->get('link'),
                $request->request->get('openInNewWindow')
            );
            $pr = new EditResponse();
            $pr->setMessage(t('External Link updated.'));
            $pr->setPage($c);
            $pr->outputJSON();
        }
    }
}
