<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Router {

	private static $instance = null;
	protected $themePaths = array();

	public static function get() {
		if (null === self::$instance) {
			self::$instance = new Router;
		}
		return self::$instance;
	}

	/**
	 * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
	 * @access public
	 * @param $path string
	 * @param $theme object, if null site theme is default
	 * @return void
	*/
	public function setThemeByPath($path, $theme = NULL, $wrapper = FILENAME_THEMES_VIEW) {
		$this->themePaths[$path] = array($theme, $wrapper);
	}

	/**
	 * This grabs the theme for a particular path, if one exists in the themePaths array 
	 * @access private
     * @param string $path
	 * @return string $theme
	*/
	public function getThemeFromPath($path) {
		// there's probably a more efficient way to do this
		$theme = false;
		$txt = Loader::helper('text');
		foreach($this->themePaths as $lp => $layout) {
			if ($txt->fnmatch($lp, $path)) {
				$theme = $layout;
				break;
			}
		}
		return $theme;
	}




}
