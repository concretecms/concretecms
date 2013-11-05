<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Tool extends Controller {

	public function getTheme() {
		return false;
	}
	
	public function display($tool) {
		$env = Environment::get();
		$query = false;
		if (substr($tool, 0, 9) != 'required/') {
			if (file_exists(DIR_BASE . '/' . DIRNAME_TOOLS . '/' . $tool . '.php')) {
				$query = $tool;
			}
		} else {
			$tool = substr($tool, 9);
			if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_TOOLS . '/' . $tool . '.php')) {
				$query = $tool;
			}
		}

		if ($query) {
			$v = new DialogView($query);
			$v->setViewRootDirectoryName(DIRNAME_TOOLS);
			$this->setViewObject($v);		
		}
	}

	public function displayBlock($btHandle, $tool) {
		$bt = BlockType::getByHandle($btHandle);
		$env = Environment::get();
		if (is_object($bt)) {
			$pkgHandle = $bt->getPackageHandle();
			$r = $env->getRecord(DIRNAME_BLOCKS . '/' . $btHandle . '/' . DIRNAME_TOOLS . '/' . $tool . '.php', $pkgHandle);
			if ($r->exists()) {
				$v = new DialogView($btHandle . '/' . DIRNAME_TOOLS . '/' . $tool);
				$v->setViewRootDirectoryName(DIRNAME_BLOCKS);
				$this->setViewObject($v);		
			}
		}
	}

}

