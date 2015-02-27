<?php
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use PageEditResponse;
use \Concrete\Core\Attribute\Set as AttributeSet;
use PermissionKey;
use Concrete\Core\Page\Collection\Version\Version;
use User;

class Seo extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/seo';
    protected $controllerActionPath = '/ccm/system/panels/details/page/seo';
    protected $validationToken = '/panels/details/page/seo';

	protected function canAccess() {
		return $this->permissions->canEditPageContents() || $this->asl->allowEditPaths();
	}

	public function on_start() {
		parent::on_start();
		$pk = PermissionKey::getByHandle('edit_page_properties');
		$pk->setPermissionObject($this->page);
		$this->asl = $pk->getMyAssignment();
	}

	public function view() {
		$as = AttributeSet::getByHandle('seo');
		$attributes = $as->getAttributeKeys();
		$this->set('attributes', $attributes);
		$this->set('allowEditPaths', $this->asl->allowEditPaths());
		$this->set('allowEditName', $this->asl->allowEditName());
	}

	public function submit() {
		if ($this->validateAction()) {
			$nvc = $this->page->getVersionToModify();

			if ($this->asl->allowEditPaths()) {
				$data = array('cHandle' => $_POST['cHandle']);
				$nvc->update($data);
			}

			if ($this->asl->allowEditName()) {
				$data = array('cName' => $_POST['cName']);
				$nvc->update($data);
			}

			$as = AttributeSet::getByHandle('seo');
			$attributes = $as->getAttributeKeys();
			foreach($attributes as $ak) {
				$ak->saveAttributeForm($nvc);
			}

            if ($this->request->request->get('sitemap')
                && $this->permissions->canApprovePageVersions()
                && \Config::get('concrete.misc.sitemap_approve_immediately')) {

                $pkr = new ApprovePageRequest();
                $u = new User();
                $pkr->setRequestedPage($this->page);
                $v = Version::get($this->page, "RECENT");
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $response = $pkr->trigger();
                $u->unloadCollectionEdit();
            }

			$r = new PageEditResponse($e);
			$r->setPage($this->page);
			$r->setTitle(t('Page Updated'));
			$r->setMessage(t('The SEO information has been saved.'));
			$r->outputJSON();

		}
	}

}