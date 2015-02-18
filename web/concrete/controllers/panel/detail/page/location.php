<?php
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use PageEditResponse;
use PermissionKey;
use Exception;
use Loader;
use PageType;
use Permissions;
use User;
use Page;
use Request;
use Concrete\Core\Page\Collection\Version\Version;
use Database;
use \Concrete\Core\Workflow\Request\MovePageRequest as MovePagePageWorkflowRequest;
use \Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;

class Location extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/location';
    protected $controllerActionPath = '/ccm/system/panels/details/page/location';
    protected $validationToken = '/panels/details/page/location';

	protected function canAccess() {
		return ($this->page->getCollectionID() != HOME_CID && is_object($this->asl) && $this->asl->allowEditPaths());
	}

	public function on_start() {
		parent::on_start();
		$pk = PermissionKey::getByHandle('edit_page_properties');
		$pk->setPermissionObject($this->page);
		$this->asl = $pk->getMyAssignment();
	}

	public function view() {
		$c = $this->page;
		$this->requireAsset('core/sitemap');
		$cParentID = $c->getCollectionParentID();
		if ($c->isPageDraft()) {
			$cParentID = $c->getPageDraftTargetParentPageID();
		}
		$this->set('parent', Page::getByID($cParentID, 'ACTIVE'));
		$this->set('cParentID', $cParentID);
        $this->set('additionalPaths', $c->getAdditionalPagePaths());
	}

	public function submit() {
        $r = new PageEditResponse();
		if ($this->validateAction()) {
			$oc = $this->page;
			$successMessage = false;
			$ocp = new Permissions($oc);
			if ($oc->getCollectionParentID() != $_POST['cParentID']) {
				$dc = Page::getByID($_POST['cParentID'], 'RECENT');
				if (!is_object($dc) || $dc->isError()) {
					throw new Exception('Invalid parent page.');
				}
				$dcp = new Permissions($dc);
				$ct = PageType::getByID($this->page->getPageTypeID());
				if (!$dcp->canAddSubpage($ct)) {
					throw new Exception('You do not have permission to add this subpage here.');
				}
				if (!$oc->canMoveCopyTo($dc)) {
					throw new Exception('You cannot add a page beneath itself.');
				}

				if ($oc->isPageDraft()) { 
					$oc->setPageDraftTargetParentPageID($dc->getCollectionID());
				} else {
                    $u = new User();
					$pkr = new MovePagePageWorkflowRequest();
					$pkr->setRequestedPage($oc);
					$pkr->setRequestedTargetPage($dc);
					$pkr->setSaveOldPagePath(false);
					$pkr->setRequesterUserID($u->getUserID());
					$u->unloadCollectionEdit($oc);
			        $response = $pkr->trigger();
                    if ($response instanceof WorkflowProgressResponse && !$this->request->request->get('sitemap')) {
                        $nc = Page::getByID($oc->getCollectionID());
                        $r->setRedirectURL(Loader::helper('navigation')->getLinkToCollection($nc));
                    }
				}
			}

            // now we do additional page URLs
            $oc->clearAdditionalPagePaths();

            $req = Request::getInstance();
            if ($req->request->has('additionalPath')) {
                $additionalPath = (array) $req->request->get('additionalPath');
                foreach($additionalPath as $path) {
                    $oc->addAdditionalPagePath($path, false);
                }
            }

            Database::get()->getEntityManager()->flush();

			$r->setTitle(t('Page Updated'));
			$r->setMessage(t('Page location information saved successfully.'));
			$r->setPage($this->page);
			$nc = Page::getByID($this->page->getCollectionID(), 'ACTIVE');
			$r->outputJSON();
		}
	}

}