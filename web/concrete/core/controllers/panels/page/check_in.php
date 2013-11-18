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
			if ($this->request->request->get('approve') == 'APPROVE' && $this->permissions->canApprovePageVersions()) {
				$pkr = new ApprovePagePageWorkflowRequest();
				$pkr->setRequestedPage($c);
				$pkr->setRequestedVersionID($v->getVersionID());
				$pkr->setRequesterUserID($u->getUserID());
				$u->unloadCollectionEdit($c);
				$response = $pkr->trigger();
			} 

			if ($this->request->request->get('approve') == 'DISCARD' && $v->canDiscard()) {
				$v->discard();
			} else {
				$v->removeNewStatus();
			}
			$nc = Page::getByID($c->getCollectionID(), $v->getVersionID());
			$u->unloadCollectionEdit();
			$r = Redirect::page($nc);
			return $r;
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

