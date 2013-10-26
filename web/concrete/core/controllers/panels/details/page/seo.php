<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Seo extends PanelController {

	protected $viewPath = '/system/panels/details/page/seo';

	protected function canViewPanel() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {
		$as = AttributeSet::getByHandle('seo');
		$attributes = $as->getAttributeKeys();
		$this->set('attributes', $attributes);
	}

	public function submit() {
		if ($this->validateSubmitPanel()) {
			$nvc = $this->page->getVersionToModify();
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