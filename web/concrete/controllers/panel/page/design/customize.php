<?
namespace Concrete\Controller\Panel\Page\Design;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Permissions;
use Page;
use stdClass;
use PermissionKey;
use \Concrete\Core\Page\Theme\EditableStyle\EditableStyle as PageThemeEditableStyle;
use PageTheme;
use PageEditResponse;
use Request;
use Loader;
use User;
use Response;

class Customize extends BackendInterfacePageController {

	protected $viewPath = '/panels/page/design/customize';
	protected $helpers = array('form');

	public function canAccess() {
		return $this->permissions->canEditPageTheme();
	}

	public function view($pThemeID) {
		$pt = PageTheme::getByID($pThemeID);
		$styles = false;
		if ($this->page->hasPageThemeCustomizations()) {
			$styles = $this->page->getCustomThemeStyles();
		}

		$styles = $pt->getEditableStylesList($styles);


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

	public function apply_to_page($pThemeID) {
		if ($this->validateAction()) {
			$pt = PageTheme::getByID($pThemeID);
			$nvc = $this->page->getVersionToModify();
			$values = $pt->mergeStylesFromPost($_POST);
			$nvc->updateCustomThemeStyles($values);
			$r = new PageEditResponse();
			$r->setPage($this->page);
			$r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
			$r->outputJSON();
		}
	}

	public function reset_page_customizations() {
		if ($this->validateAction()) {
			$nvc = $this->page->getVersionToModify();
			$nvc->resetCustomThemeStyles();
			$r = new PageEditResponse();
			$r->setPage($this->page);
			$r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
			$r->outputJSON();
		}
	}

	public function reset_site_customizations($pThemeID) {
		if ($this->validateAction()) {
			$pt = PageTheme::getByID($pThemeID);
			$pt->reset();
			$r = new PageEditResponse();
			$r->setPage($this->page);
			$r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
			$r->outputJSON();
		}
	}


	public function apply_to_site($pThemeID) {
		$pk = PermissionKey::getByHandle('customize_themes');
		if ($this->validateAction() && $pk->can()) {
			$pt = PageTheme::getByID($pThemeID);
			$values = $pt->mergeStylesFromPost($_POST);
			$pt->saveEditableStyles($values);
			$r = new PageEditResponse();
			$r->setPage($this->page);
			$r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $this->page->getCollectionID());
			$r->outputJSON();
		}
	}

	public function preview($pThemeID) {
		$req = Request::getInstance();
		$req->setCurrentPage($this->page);
		$controller = $this->page->getPageController();
		$view = $controller->getViewObject();
		$pt = PageTheme::getByID($pThemeID);
		$view->setCustomPageTheme($pt);
		$sheets = $pt->getStyleSheets();
		$styleMap = array();
		$u = new User();
		$date = date('Y-m-d H:i');		
		foreach($sheets as $file) {
			ob_start();
			$values = $pt->mergeStylesFromPost($_POST);
			$pt->outputStyleSheet($file, $values);
			$tmpFile = md5($u->getUserID() . ':' . $date . ':' . $file) . '.css';
			$styleMap[$file] = DIRNAME_PREVIEW . '/' . $tmpFile . '?' . time();
			$cacheFile = DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $pt->getThemeHandle() . '/' . DIRNAME_PREVIEW . '/' . $tmpFile;
			if (!file_exists(dirname($cacheFile))) {
				mkdir(dirname($cacheFile), DIRECTORY_PERMISSIONS_MODE, true);
			}
			$r = file_put_contents($cacheFile, ob_get_contents());
			ob_end_clean();
		}
		$view->setCustomStyleMap($styleMap);
		$req->setCustomRequestUser(-1);
		$response = new Response();
		$content = $view->render();
		$response->setContent($content);
		return $response;
	}

}