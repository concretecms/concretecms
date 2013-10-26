<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Page_CheckIn extends PanelController {

	protected $viewPath = '/system/panels/page/check_in';
	public function canViewPanel() {
		return $this->permissions->canApprovePageVersions() || $this->permissions->canViewPageVersions();
	}

	public function submit() {
		if ($this->validateSubmitPanel()) {
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

			$u->unloadCollectionEdit();
			$r = Redirect::page($c);
			return $r;
		}
	}

	protected function validateSubmitPanel() {
		if (parent::validateSubmitPanel()) {
			if ($this->permissions->canEditPageContents() || $this->permissions->canEditPageProperties() || $this->permissions->canApprovePageVersions()) {
				return true;
			}
		}
	}
}

