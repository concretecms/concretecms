<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\EditResponse;

class EditAlias extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/edit_alias';

    public function view()
    {
        $this->set('form', $this->app->make('helper/form'));
        $this->set('customAliasName', $this->page->getCustomAliasName());
        $this->set('aliasHandle', $this->page->getAliasHandle());
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $data = [];
            $customAliasName = $this->request->request->get('customAliasName');
            if (is_string($customAliasName)) {
                $data['customAliasName'] = trim($customAliasName);
            }
            $aliasHandle = $this->request->request->get('aliasHandle');
            if (is_string($aliasHandle) && ($aliasHandle = trim($aliasHandle)) !== '') {
                $data['aliasHandle'] = $aliasHandle;
            }
            $this->page->updateCollectionAlias($data);
            $pr = new EditResponse();
            $pr->setMessage(t('Alias updated.'));
            $pr->setPage($this->page);

            return $this->app->make(ResponseFactoryInterface::class)->json($pr);
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageProperties() && $this->page->isAliasPage();
    }
}
