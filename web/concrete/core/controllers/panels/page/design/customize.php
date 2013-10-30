<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Page_Design_Customize extends FrontendEditPageController {

	protected $viewPath = '/system/panels/page/design/customize';
	protected $helpers = array('form');

	public function canAccess() {
		$pk = PermissionKey::getByHandle('customize_themes');
		return $this->permissions->canEditPageTheme() && $pk->can();
	}

	public function view($pThemeID) {
		$pt = PageTheme::getByID($pThemeID);
		$styles = $pt->getEditableStylesList();

		$sets = array();
		$type = 0;
		foreach($styles as $style) {
			if ($style->getType() == PageThemeEditableStyle::TSTYPE_CUSTOM) {
				continue;
			}
			if ($style->getType() != $type) {
				if (is_object($set)) {
					$sets[] = $set;
				}
				$set = new stdClass;
				$set->title = $style->getTypeHeaderName();	
				$set->styles = array();
			}
			$set->styles[] = $style;
			$type = $style->getType();
		}
		if (is_object($set)) {
			$sets[] = $set;
		}

		$this->set('styleSets', $sets);
		$this->set('theme', $pt);
		$this->set('styles', $styles);
	}

	public function submit() {
		if ($this->validateAction()) {

			$r = new PageEditResponse();
			$r->setPage($c);
			$r->setMessage(t('Page theme updated successfully.'));
			$r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
			$r->outputJSON();
		}
	}
}