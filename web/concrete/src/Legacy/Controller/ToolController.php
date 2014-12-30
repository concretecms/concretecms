<?php
namespace Concrete\Core\Legacy\Controller;
use Controller;
use Environment;
use BlockType;
use \Concrete\Core\View\DialogView;
use Package;

class ToolController extends Controller {

	public function getTheme() {
		return false;
	}

	public function display($tool) {
		$query = false;
		$package = false;

		if (substr($tool, 0, 9) == 'packages/') {
			$slashPos = strpos($tool, '/', 9);
			$packageHandle = substr($tool, 9, $slashPos - 9);
			$tool = substr($tool, $slashPos + 1);
			$package = Package::getByHandle($packageHandle);

			if ($package !== null && file_exists($package->getPackagePath() . '/' . DIRNAME_TOOLS . '/' . $tool . '.php')) {
				$query = $tool;
			}
		} elseif (substr($tool, 0, 9) != 'required/') {
			if (file_exists(DIR_APPLICATION . '/' . DIRNAME_TOOLS . '/' . $tool . '.php')) {
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
			if ($package !== false) {
				$v->setPackageHandle($package->getPackageHandle());
			}
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
				$v->setInnerContentFile($r->file);
				$this->setViewObject($v);
			}
		}
	}

}

