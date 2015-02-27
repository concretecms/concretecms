<?php
namespace Concrete\Controller\Dialog\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use PageType;
use Loader;
use URL;
use Concrete\Core\Page\EditResponse;

class AddExternal extends BackendInterfacePageController {

	protected $viewPath = '/dialogs/page/add_external';

	protected function canAccess() {
		return $this->permissions->canAddExternalLink();
	}

	public function view() {

	}

    public function submit() {
        if ($this->validateAction()) {
            $request = \Request::getInstance();
            $this->page->addCollectionAliasExternal(
                $request->request->get('name'),
                $request->request->get('link'),
                $request->request->get('openInNewWindow')
            );

            $r = new EditResponse();
            $r->setPage($this->page);
            $r->setRedirectURL(URL::to('/dashboard/sitemap'));
            $r->outputJSON();
        }
    }

}

