<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Seo extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/details/page/seo';

	protected function canAccess() {
		return $this->permissions->canEditPageContents() || $this->asl->allowEditPaths();
	}

	public function __construct() {
		parent::__construct();
		$pk = PermissionKey::getByHandle('edit_page_properties');
		$pk->setPermissionObject($this->page);
		$this->asl = $pk->getMyAssignment();
	}

	public function view() {
		$as = AttributeSet::getByHandle('seo');
		$attributes = $as->getAttributeKeys();
		$this->set('attributes', $attributes);
		$this->set('allowEditPaths', $this->asl->allowEditPaths());
	}

	public function submit() {
		if ($this->validateAction()) {
			$nvc = $this->page->getVersionToModify();

			if ($this->asl->allowEditPaths()) {
				$data = array('cHandle' => $_POST['cHandle']);
				$nvc->update($data);
			}
			
			$as = AttributeSet::getByHandle('seo');
			$attributes = $as->getAttributeKeys();
			foreach($attributes as $ak) {
				$ak->saveAttributeForm($nvc);
			}
			$r = new PageEditResponse($e);
			$r->setPage($this->page);
			$r->setMessage(t('The SEO information has been saved.'));
			$r->outputJSON();
		}
	}

}