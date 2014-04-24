<?
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use PageEditResponse;
use PageType;
use View;
use Loader;
use PageTemplate;
use User;

class Composer extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/composer';

	protected function canAccess() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {
		$this->requireAsset('core/composer');
		$pagetype = PageType::getByID($this->page->getPageTypeID());
		$id = $this->page->getCollectionID();
		$saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
		$viewURL = View::url('/dashboard/composer/write', 'draft', $id);
		$this->set('saveURL', $saveURL);
		$this->set('viewURL', $viewURL);
		$this->set('pagetype', $pagetype);
	}

	public function autosave() {
		$r = $this->save();
		$ptr = $r[0];
		if (!$ptr->error->has()) {
			$ptr->setMessage(t('Page saved on %s', $ptr->time));
		}
		$ptr->outputJSON();
	}

	public function publish() {
		$r = $this->save();
		$ptr = $r[0];
		$pagetype = $r[1];
		$outputControls = $r[2];

		$c = $this->page;
		$e = Loader::helper('validation/error');
		if (!$c->getPageDraftTargetParentPageID()) {
			$e->add(t('You must choose a page to publish this page beneath.'));
		}

		foreach($outputControls as $oc) {
			if ($oc->isPageTypeComposerFormControlRequiredOnThisRequest()) {
				$r = $oc->validate();
				if ($r instanceof \Concrete\Core\Error\Error) {
					$e->add($r);
				}
			}
		}

		$ptr->setError($e);

		if (!$e->has()) {
			$pagetype->publish($c);
			$ptr->setRedirectURL(Loader::helper('navigation')->getLinkToCollection($c));
		}
		$ptr->outputJSON();
	}

	public function discard() {
		$ptr = new PageEditResponse();
		if ($this->permissions->canDeletePage() && $this->page->isPageDraft()) {
			$this->page->delete();
			$u = new User();
			$cID = $u->getPreviousFrontendPageID();
			$ptr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
		} else {
			$e = Loader::helper('validation/error');
			$e->add(t('You do not have permission to discard this page.'));
			$ptr->setError($e);
		}

		$ptr->outputJSON();
	}

	protected function save() {
		$c = $this->page;
		$ptr = new PageEditResponse($e);
		$ptr->setPage($c);

		$pagetype = $c->getPageTypeObject();
		$pt = PageTemplate::getByID($_POST['ptComposerPageTemplateID']);
		$availablePageTemplates = $pagetype->getPageTypePageTemplateObjects();
		if (!is_object($pt)) {
			$pt = $pagetype->getPageTypeDefaultPageTemplateObject();
		}
		$e = $pagetype->validateCreateDraftRequest($pt);
		if (!$e->has()) {
			$c = $c->cloneVersion('');

			/// set the target
			$configuredTarget = $pagetype->getPageTypePublishTargetObject();
			$targetPageID = $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
			if (!$targetPageID) {
				$targetPageID = $_POST['cParentID'];
			}

			$c->setPageDraftTargetParentPageID($targetPageID);
			$outputControls = $pagetype->savePageTypeComposerForm($c);
		}
		return array($ptr, $pagetype, $outputControls);
	}

}