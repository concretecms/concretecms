<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Page_CheckIn extends BackendInterfacePageController {

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
							if ($r instanceof ValidationErrorHelper) {
								$e->add($r);
							}
						}						
					}
				}

				$pr->setError($e);
				if (!$e->has()) {
					$pkr = new ApprovePagePageWorkflowRequest();
					$pkr->setRequestedPage($c);
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$pr->setMessage(t('Page approval requested.'));
					$u->unloadCollectionEdit($c);
					$response = $pkr->trigger();
				}
			} else if ($this->request->request->get('action') == 'discard' && $v->canDiscard()) {
				$v->discard();
				$pr->setMessage(t('Page version discarded.'));
			} else {
				$pr->setMessage(t('Page saved.'));
				$v->removeNewStatus();
			}
			$nc = Page::getByID($c->getCollectionID(), $v->getVersionID());
			$u->unloadCollectionEdit();
			$pr->setRedirectURL(Loader::helper('navigation')->getLinkToCollection($nc));
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

