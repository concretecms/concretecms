<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_PageRequestView extends RequestView {

	protected $c; // page

	public function getPageObject() {
		return $this->c;
	}

	/** 
	 * Begin the render
	 */
	public function start($page) {
		$this->c = $page;
		parent::start($page->getCollectionPath());
	}

	public function getScopeItems() {
		$items = parent::getScopeItems();
		$items['c'] = $this->c;
		return $items;
	}

	public function inc($file, $args = array()) {
		extract($args);
		extract($this->getScopeItems());
		$env = Environment::get();
		include($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . $file, $this->themePkgHandle));
	}

	protected function setupController() {
		if (!isset($this->controller)) {
			$this->controller = Loader::controller($this->c);
			$this->controller->setupAndRun();
		}
	}

	protected function loadRequestViewThemeObject() {
		$theme = $this->c->getCollectionThemeObject();
		if (is_object($theme)) {
			$this->themeHandle = $theme->getThemeHandle();
		}
		parent::loadRequestViewThemeObject();
	}

	public function setupRender() {
		$this->loadRequestViewThemeObject();
		$env = Environment::get();
		$this->setInnerContentFile($env->getPath(DIRNAME_PAGES . '/' . trim($this->viewPath, '/') . '.php', $this->themePkgHandle));
		if (file_exists(DIR_FILES_THEMES_CORE . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php')) {
			$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $this->themeHandle . '.php'));
		} else {
			$this->setViewTemplate($env->getPath(DIRNAME_THEMES . '/' . $this->themeHandle . '/' . FILENAME_THEMES_VIEW, $this->themePkgHandle));
		}
	}

	public function deliverRender($contents) {
		$contents = parent::deliverRender($contents);
		// do full page caching
		print $contents;
	}

	/** 
	 * @deprecated
	 */
	public function getCollectionObject() {return $this->getPageObject();}
	

}