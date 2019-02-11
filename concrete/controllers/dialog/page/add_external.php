<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class AddExternal extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/add_external';

    public function view()
    {
        $this->set('form', $this->app->make('helper/form'));
        $this->set('name', '');
        $this->set('link', '');
        $this->set('openInNewWindow', true);
        $this->set('isEditingExisting', false);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $post = $this->request->request;
            $this->page->addCollectionAliasExternal(
                $post->get('name'),
                $post->get('link'),
                $post->get('openInNewWindow')
            );

            $r = new EditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL($this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/sitemap']));
            $r->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canAddExternalLink();
    }
}
