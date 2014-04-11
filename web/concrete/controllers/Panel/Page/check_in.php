<?
namespace Concrete\Controller\Panel\Page;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
use Permissions;
use \Concrete\Core\Page\Collection\Version\Version as CollectionVersion;
use Loader;
use Page;
use User;
use Response;
use \Concrete\Helper\Validation\Error as ValidationErrorHelper;
use Redirect;
use \Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use \Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use PageEditResponse;
use \Concrete\Core\Page\Type\Composer\Control\Control as PageTypeComposerControl;
class CheckIn extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/page/check_in';
	public function canAccess() {
		return $this->permissions->canApprovePageVersions() || $this->permissions->canViewPageVersions();
	}

	public function submit() {
		if ($this->validateAction()) {
			$c = $this->page;
			$u = new User();
			$v = CollectionVersion::get($c, "RECENT");
			$v->setComment($_REQUEST['comments']);
			$pr = new PageEditResponse();
			if ($this->request->request->get('action') == 'publish' && $this->permissions->canApprovePageVersions()) {

				// verify this page type has all the items necessary to be approved.
				$e = Loader::helper('validation/error');
				if ($c->isPageDraft()) {
					if (!$c->getPageDraftTargetParentPageID()) {
						$e->add(t('You must choose a page to publish this page beneath.'));
					}
				}
				$pagetype = $c->getPageTypeObject();
				if (is_object($pagetype)) {
					$controls = PageTypeComposerControl::getList($pagetype);
					foreach($controls as $oc) {
						if ($oc->isPageTypeComposerFormControlRequiredOnThisRequest()) {
							$oc->setPageObject($c);
							$r = $oc->validate();
							if ($r instanceof \Concrete\Helper\Validation\Error) {
								$e->add($r);
							}
						}						
					}
				}

				if ($c->isPageDraft() && !$e->has()) {
					$targetParentID = $c->getPageDraftTargetParentPageID();
					if ($targetParentID) {
						$tp = Page::getByID($targetParentID, 'ACTIVE');
						$pp = new Permissions($tp);
						if (!is_object($tp) || $tp->isError()) {
							$e->add(t('Invalid target page.'));
						} else if (!$pp->canAddSubCollection($pagetype)) {
							$e->add(t('You do not have permissions to add a page of this type in the selected location.'));
						}
					}
				}

				$pr->setError($e);
				if (!$e->has()) {
					$pkr = new ApprovePagePageWorkflowRequest();
					$pkr->setRequestedPage($c);
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$u->unloadCollectionEdit($c);
					$response = $pkr->trigger();
					$pr->setMessage(t('Page approved!'));
					if ($response instanceof WorkflowProgressResponse) {
						// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
						$pr->setMessage(t('Page approval requested. You must complete this workflow to approve the page.'));
					}

					if ($c->isPageDraft()) {
						$pagetype->publish($c);
						$pr->setMessage(t('Page published'));
					}
				}
			} else if ($this->request->request->get('action') == 'discard') {
				if ($c->isPageDraft() && $this->permissions->canDeletePage()) {
					$this->page->delete();
					$u = new User();
					$cID = $u->getPreviousFrontendPageID();
					$pr->setMessage(t('Draft discarded.'));
					$pr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
				} else if ($v->canDiscard()) {
					$v->discard();
					$pr->setMessage(t('Page version discarded.'));
				}
			} else {
				$pr->setMessage(t('Page saved.'));
				$v->removeNewStatus();
			}
			$nc = Page::getByID($c->getCollectionID(), $v->getVersionID());
			$u->unloadCollectionEdit();
			$pr->setRedirectURL(Loader::helper('navigation')->getLinkToCollection($nc, true));
			$pr->outputJSON();
		}
	}

	public function exitEditMode($cID, $token) {
		if (Loader::helper('validation/token')->validate('', $token)) {
			$c = Page::getByID($cID);
			$cp = new Permissions($c);
			if ($cp->canViewToolbar()) {
				$u = new User();
				$u->unloadCollectionEdit();
			}
			return Redirect::page($c);
		}
	
		return new Response(t('Access Denied'));
	}

	protected function validateAction() {
		if (parent::validateAction()) {
			if ($this->permissions->canEditPageContents() || $this->permissions->canEditPageProperties() || $this->permissions->canApprovePageVersions()) {
				return true;
			}
		}
	}
}

