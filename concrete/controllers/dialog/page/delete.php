<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Workflow\Request\DeletePageRequest as DeletePagePageWorkflowRequest;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use User;
use Page;

class Delete extends BackendInterfacePageController
{
    protected $viewPath = '/dialogs/page/delete';
    protected $controllerActionPath = '/ccm/system/dialogs/page/delete';

    protected function canAccess()
    {
        return $this->permissions->canDeletePage();
    }

    public function view()
    {
        $this->set('numChildren', $this->page->getNumChildren());
    }

    public function viewFromSitemap()
    {
        $this->set('numChildren', $this->page->getNumChildren());
        $this->set('sitemap', true);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $c = $this->page;
            $cp = $this->permissions;
            $u = new User();
            if ($cp->canDeletePage() && $c->getCollectionID() != HOME_CID && (!$c->isMasterCollection())) {
                $children = $c->getNumChildren();
                if ($children == 0 || $u->isSuperUser()) {
                    if ($c->isExternalLink()) {
                        $c->delete();
                    } else {
                        $pkr = new DeletePagePageWorkflowRequest();
                        $pkr->setRequestedPage($c);
                        $pkr->setRequesterUserID($u->getUserID());
                        $u->unloadCollectionEdit($c);
                        $response = $pkr->trigger();
                        $pr = new PageEditResponse();
                        $pr->setPage($c);
                        $parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
                        if ($response instanceof WorkflowProgressResponse) {
                            // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                            $pr->setMessage(t('Page deleted successfully.'));
                            if (!$this->request->request->get('sitemap')) {
                                $pr->setRedirectURL($parent->getCollectionLink(true));
                            }
                        } else {
                            $pr->setMessage(t('Page deletion request saved. This action will have to be approved before the page is deleted.'));
                        }
                        $pr->outputJSON();
                    }
                }
            }
        }
    }
}
