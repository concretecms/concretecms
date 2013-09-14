<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_PageTemplateRequestView extends PathRequestView {

	protected $theme;
	protected $pageTemplate;
	protected $c;
	protected $templateRequestViewAreaHandles = array();

	public function registerAreaHandle($arHandle) {
		if (!in_array($arHandle, $this->templateRequestViewAreaHandles)) {
			$this->templateRequestViewAreaHandles[] = $arHandle;
		}
	}

	public function getPageTemplateRequestViewAreaHandles() {
		return $this->templateRequestViewAreaHandles;
	}

	public function getCollectionObject() {
		return $this->c;
	}
	public function setPageTheme($theme) {
		$this->theme = $theme;
	}

	/** 
	 * Begin the render
	 */
	public function start($pageTemplate) {
		$this->pageTemplate = $pageTemplate;
		$this->themeHandle = $this->theme->getThemeHandle();
		$this->themePkgHandle = $this->theme->getPackageHandle();
		$this->c = new Page();
		$this->addScopeItems(array('c' => $this->getCollectionObject()));
	}

	protected function setupController() {
		$this->controller = new Controller();
	}

	public function finishRender() {
		return false;
	}

	public function deliverRender() {
		return false;
	}

	protected function runControllerTask() {}

	public function setupRender() {
		$env = Environment::get();
		$pt = $this->pageTemplate;
		$rec = $env->getRecord(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle);
		if ($rec->exists()) {
			$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $pt->getPageTemplateHandle() . '.php', $this->themePkgHandle));
		} else {
			$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_DEFAULT, $this->themePkgHandle));
		}
	}

}