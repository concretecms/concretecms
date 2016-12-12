<?php
namespace Concrete\Core\Block\View;
use Loader;
use Environment;
use CacheLocal;
use AssetList;
use View;
use Block;
use \Concrete\Core\Package\PackageList;
use \Concrete\Core\Asset\JavascriptAsset;
use \Concrete\Core\Asset\CssAsset;

class BlockViewTemplate {

	protected $basePath = '';

	protected $bFilename;
	protected $btHandle;
	protected $obj;
	protected $baseURL;
	protected $checkAssets = true;
	protected $itemsToCheck = array(
		'CSS' => 'view.css',
		'JAVASCRIPT' => 'view.js'
	);
	protected $render = FILENAME_BLOCK_VIEW;

	public function __construct($obj) {
		$this->btHandle = $obj->getBlockTypeHandle();
		$this->obj = $obj;
		if ($obj instanceof Block) {
			$this->bFilename = $obj->getBlockFilename();
		}
		$this->computeView();
	}

	protected function computeView() {
		$bFilename = $this->bFilename;
		$obj = $this->obj;

		// if we've passed in "templates/" as the first part, we strip that off.
		if (strpos($bFilename, 'templates/') === 0) {
			$bFilename = substr($bFilename, 10);
		}

		// The filename might be a directory name with .php-appended (BlockView does that), strip it.
		$bFilenameWithoutDotPhp = $bFilename;
		if ( substr( $bFilename, -4 ) === ".php" ) {
			$bFilenameWithoutDotPhp = substr( $bFilename, 0, strlen( $bFilename ) -4 );
		}

		if ($bFilename) {
			if (is_file(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$bv = new BlockView($obj);
				$this->baseURL = $bv->getBlockURL();
				$this->basePath = $bv->getBlockPath($this->render);
			} else if (is_file(DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$this->baseURL = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
				$this->basePath = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle();
			} else if (is_dir(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename . '/' . $this->render;
				$this->basePath = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$this->baseURL = REL_DIR_APPLICATION . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
			} else if (is_dir(DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename . '/'  . $this->render;
				$this->basePath = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$this->baseURL = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
			} else if ( $bFilename !== $bFilenameWithoutDotPhp ) {
				if (is_dir(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp)) {
					$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp . '/' . $this->render;
					$this->basePath = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp;
					$this->baseURL = REL_DIR_APPLICATION . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp;
				} else if (is_dir(DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp)) {
					$template = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp . '/'  . $this->render;
					$this->basePath = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp;
					$this->baseURL = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp;
				}
			}

			// we check all installed packages
			if (!isset($template)) {
				$pl = PackageList::get();
				$packages = $pl->getPackages();
				foreach($packages as $pkg) {
					$d = '';
					if (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) {
						$d = DIR_PACKAGES . '/'. $pkg->getPackageHandle();
					} else if (is_dir(DIR_PACKAGES_CORE . '/'. $pkg->getPackageHandle())) {
						$d = DIR_PACKAGES_CORE . '/'. $pkg->getPackageHandle();
					}

					if ($d != '') {
						$baseStub = (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle())) ? DIR_REL . '/' . DIRNAME_PACKAGES . '/'. $pkg->getPackageHandle() : ASSETS_URL . '/'. DIRNAME_PACKAGES . '/' . $pkg->getPackageHandle();

						if (is_file($d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $bFilename)) {
							$template = $d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $bFilename;
							$this->baseURL = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
							$this->basePath = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle();
						} else if (is_file($d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
							$template = $d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
							$this->baseURL = $baseStub . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
							$this->basePath = $d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
						} else if (is_dir($d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
							$template = $d . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename . '/' . $this->render;
							$this->baseURL = $baseStub . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
						}
					}

					if ($this->baseURL != '') {
						continue;
					}

				}
			}

		} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '.php')) {
			$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '.php';
			$bv = new BlockView($obj);
			$this->baseURL = $bv->getBlockURL();
			$this->basePath = $bv->getBlockPath($this->render);
		} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . $this->render)) {
			$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . $this->render;
			$this->baseURL = REL_DIR_APPLICATION . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
		}

		if (!isset($template)) {
			$bv = new BlockView($obj);
			$template = $bv->getBlockPath($this->render) . '/' . $this->render;
			$this->baseURL = $bv->getBlockURL($this->render);
		}

		if ($this->basePath == '') {
			$this->basePath = dirname($template);
		}
		$this->template = $template;
	}


	public function getBasePath() {return $this->basePath;}
	public function getBaseURL() {return $this->baseURL;}
	public function setBlockCustomTemplate($bFilename) {
		$this->bFilename = $bFilename;
		$this->computeView();
	}

	public function setBlockCustomRender($renderFilename) {
		// if we've passed in "templates/" as the first part, we strip that off.
		if (strpos($renderFilename, 'templates/') === 0) {
			$bFilename = substr($renderFilename, 10);
			$this->setBlockCustomTemplate($bFilename);
		} else {
			$this->render = $renderFilename;
		}
		$this->computeView();
	}


	public function getTemplate() {
		return $this->template;
	}

	public function registerTemplateAssets() {
		$items = array();
		$h = Loader::helper("html");
		$dh = Loader::helper('file');
		if ($this->checkAssets == false) {
			return $items;
		} else {
			$al = AssetList::getInstance();
			$v = View::getInstance();
			foreach($this->itemsToCheck as $t => $i) {
				if (file_exists($this->basePath . '/' . $i)) {
                    $identifier = substr($this->basePath, strpos($this->basePath, 'blocks'));
                    // $identifier = 'blocks/page_list', 'blocks/feature', 'blocks/page_list/templates/responsive', etc...
					switch($t) {
						case 'CSS':
							$asset = new CssAsset($identifier);
							$asset->setAssetURL($this->getBaseURL() . '/' . $i);
							$asset->setAssetPath($this->basePath . '/' . $i);
							$al->registerAsset($asset);
							$v->requireAsset('css', $identifier);
							break;
						case 'JAVASCRIPT':
							$asset = new JavascriptAsset($identifier);
							$asset->setAssetURL($this->getBaseURL() . '/' . $i);
							$asset->setAssetPath($this->basePath . '/' . $i);
							$al->registerAsset($asset);
							$v->requireAsset('javascript', $identifier);
							break;
					}
				}
			}
			$css = $dh->getDirectoryContents($this->basePath . '/' . DIRNAME_CSS);
			$js = $dh->getDirectoryContents($this->basePath . '/' . DIRNAME_JAVASCRIPT);
			if (count($css) > 0) {
				foreach($css as $i) {
					if(substr($i,-4)=='.css') {
                        $identifier = substr($this->basePath, strpos($this->basePath, 'blocks')) . '/' . $i;
						$asset = new CssAsset($identifier);
						$asset->setAssetURL($this->getBaseURL() . '/' . DIRNAME_CSS . '/' . $i);
						$asset->setAssetPath($this->basePath . '/' . DIRNAME_CSS . '/' . $i);
						$al->registerAsset($asset);
						$v->requireAsset('css', $identifier);
					}
				}
			}
			if (count($js) > 0) {
				foreach($js as $i) {
					if (substr($i,-3)=='.js') {
                        $identifier = substr($this->basePath, strpos($this->basePath, 'blocks')) . '/' . $i;
						$asset = new JavascriptAsset($identifier);
						$asset->setAssetURL($this->getBaseURL() . '/' . DIRNAME_JAVASCRIPT . '/' . $i);
						$asset->setAssetPath($this->basePath . '/' . DIRNAME_JAVASCRIPT . '/' . $i);
						$al->registerAsset($asset);
						$v->requireAsset('javascript', $identifier);
					}
				}
			}
		}
	}




}
