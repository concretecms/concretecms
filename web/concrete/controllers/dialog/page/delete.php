<?
namespace \Concrete\Controller\Dialog\Page;
use \Concrete\Controller\Backend\UI as BackendInterfaceController;
use \Concrete\Core\Workflow\Request\DeletePage as DeletePagePageWorkflowRequest;
use \Concrete\Core\Page\EditResponse as PageEditResponse;
use \Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class Delete extends BackendInterfacePageController {

	protected $viewPath = '/system/dialogs/page/delete';

	protected function canAccess() {
		return $this->permissions->canDeletePage();
	}

	public function view() {
		$this->set('numChildren', $this->page->getNumChildren());
	}

	public function submit() {
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
						} else {
							$pr->setMessage(t('Page request saved. This action will have to be approved before the page is deleted.'));
						}
						$pr->outputJSON();
					}
				}
			}
		}
	}

}

