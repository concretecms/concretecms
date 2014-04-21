<?
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
use PageEditResponse;
use \Concrete\Core\Attribute\Set as AttributeSet;
use PermissionKey;

class Seo extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/seo';

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
		print '1';
		if ($this->validateAction()) {
			print '2';
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
			$r->setTitle(t('Page Updated'));
			$r->setMessage(t('The SEO information has been saved.'));
			$r->outputJSON();
		}
		exit;
	}

}