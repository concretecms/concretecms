<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('archive');

/**
*
* This class is responsible for unpacking themes that have been zipped and uploaded to the system. 
* @package Pages
* @subpackage Themes
*/
class Concrete5_Model_PageThemeArchive extends Archive {
	
	// takes a zip file and tries to unpack it to the theme directory on the site
	
	public function install($file, $inplace=false) {
		parent::install($file, $inplace);
	}
	
	public function __construct($theme = null) {
		parent::__construct();
		$this->targetDirectory = DIR_FILES_THEMES;
		if ($theme) {
			$this->_theme = $theme;
		}
	}
	
	public function uninstall() {
		// can only uninstall non-global ones
		if ($this->_theme) {
			$theme = $this->_theme;
			if (file_exists(DIR_FILES_THEMES . '/' . $theme) && $theme != '') {
				$r = @rename(DIR_FILES_THEMES . '/' . $theme, DIR_FILES_TRASH . '/' . $theme  . time());
				if (!$r) {
					throw new Exception(t('Unable to uninstall %s theme by moving it to the trash.', $theme));
					return false;
				}
			}
			/* else {
				throw new Exception('You may only remove themes that are contained within your website\'s /themes directory.');
			}*/
			
		}
	}
	
}
