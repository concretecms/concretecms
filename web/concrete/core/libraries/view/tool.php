<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_ToolView extends View {
	
	private static $loc = null;

	public static function getInstance() {
		if (null === self::$loc) {
			self::$loc = new self;
		}
		return self::$loc;
	}

	public function outputAssetIntoView($item) {
		$str = '<script type="text/javascript">';	
		if ($item instanceof CssAsset) {
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "CSS")';
		} else {
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "JAVASCRIPT")';
		}
		$str .= '</script>';
		return $str;
	}


	public function render(Request $co) {
		$env = Environment::get();
		switch($co->getIncludeType()) {
			case "CONCRETE_TOOL":
			case "TOOL":
				$r = $env->getPath(DIRNAME_TOOLS . '/' . $co->getFilename());
				break;
			case 'PACKAGE_TOOL':
				$r = $env->getPath(DIRNAME_TOOLS . '/' . $co->getFilename(), $co->getPackageHandle());
				break;
			case "BLOCK_TOOL":
				if ($co->getBlock() != '') {
					$bt = BlockType::getByHandle($co->getBlock());
					if ($bt->getPackageID() > 0) {
						$r = $env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename(), $bt->getPackageHandle());
					} else {
						$r = $env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename());
					}
				}
				break;
		}

		ob_start();
		if (file_exists($r)) {
			include($r);
		}
		$_contents = ob_get_contents();
		ob_end_clean();

		$_req = Request::get(); // update the request;
		$addOutputAssets = $_req->getRequiredAssetsToOutput();
		if (count($addOutputAssets)) {
			ob_start();
			$this->outputHeaderItems();
			print $_contents;
			$this->outputFooterItems();
			$_contents = ob_get_contents();
			ob_end_clean();

			foreach($addOutputAssets as $outputAsset) {
				$this->addOutputAsset($outputAsset);
			}

			$_contents = $this->replaceAssetPlaceholders($_contents);

			// replace any empty placeholders
			$_contents = $this->replaceEmptyAssetPlaceholders($_contents);
		}
		
		print $_contents;

		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
}
