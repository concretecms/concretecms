<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_ToolRequestView extends RequestView {
	
	protected $request;

	public function start($request) {
		$this->request = $request;
	}
	public function action($action) {
		throw new Exception(t('Action is not available in a tools context.'));
	}

	protected function setupController() {}
	protected function runControllerTask() {}
	public function startRender() {}
	public function setupRender() {
		$env = Environment::get();
		switch($this->request->getIncludeType()) {
			case "CONCRETE_TOOL":
			case "TOOL":
				$r = $env->getPath(DIRNAME_TOOLS . '/' . $this->request->getFilename());
				break;
			case 'PACKAGE_TOOL':
				$r = $env->getPath(DIRNAME_TOOLS . '/' . $this->request->getFilename(), $this->request->getPackageHandle());
				break;
			case "BLOCK_TOOL":
				if ($co->getBlock() != '') {
					$bt = BlockType::getByHandle($this->request->getBlock());
					if ($bt->getPackageID() > 0) {
						$r = $env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $this->request->getFilename(), $bt->getPackageHandle());
					} else {
						$r = $env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $this->request->getFilename());
					}
				}
				break;
		}
		$this->setViewTemplate($r);
	}

	public function getScopeItems() {return array();}
	public function finishRender() {
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}

	protected function onBeforeGetContents() {
		$this->markHeaderAssetPosition();
	}

	public function outputAssetIntoView($item) {
		$str = '<script type="text/javascript">';	
		if ($item instanceof CssAsset) {
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "CSS")';
		} else {
			$str .= 'ccm_addHeaderItem("' . $item->getAssetURL() . '", "JAVASCRIPT")';
		}
		$str .= '</script>';
		print $str . "\n";
	}

	protected function onAfterGetContents() {
		// now that we have the contents of the tool,
		// we make sure any require assets get moved into the header
		// since that's the only place they work in the AJAX output.
		$r = Request::get();
		$assets = $r->getRequiredAssetsToOutput();
		foreach($assets as $asset) {
			$asset->setAssetPosition(Asset::ASSET_POSITION_HEADER);
			$this->addOutputAsset($asset);
		}
	}
}
