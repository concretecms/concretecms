<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Blocks
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents a block's template, whether it's built-in, or custom.
 *
 * @package Blocks
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Concrete5_Library_BlockViewTemplate {

	protected $basePath = '';
	
	protected $bFilename;
	protected $btHandle;
	protected $obj;
	protected $baseURL;
	protected $checkHeaderItems = true;
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
				$bv = new BlockView();
				$bv->setBlockObject($obj);
				$this->baseURL = $bv->getBlockURL($this->render);
				$this->basePath = $bv->getBlockPath($this->render);
			} else if (is_file(DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$this->baseURL = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
				$this->basePath = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle();
			} else if (is_dir(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename . '/' . $this->render;
				$this->basePath = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$this->baseURL = DIR_REL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
			} else if (is_dir(DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename)) {
				$template = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename . '/'  . $this->render;
				$this->basePath = DIR_FILES_BLOCK_TYPES_CORE . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
				$this->baseURL = ASSETS_URL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilename;
			} else if ( $bFilename !== $bFilenameWithoutDotPhp ) {
				if (is_dir(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp)) {
					$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp . '/' . $this->render;
					$this->basePath = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp;
					$this->baseURL = DIR_REL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES . '/' . $bFilenameWithoutDotPhp;
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
			$bv = new BlockView();
			$bv->setBlockObject($obj);
			$this->baseURL = $bv->getBlockURL($this->render);
			$this->basePath = $bv->getBlockPath($this->render);
		} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . $this->render)) {
			$template = DIR_FILES_BLOCK_TYPES . '/' . $obj->getBlockTypeHandle() . '/' . $this->render;
			$this->baseURL = DIR_REL . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle();
		}
		
		if (!isset($template)) {
			$bv = new BlockView();
			$bv->setBlockObject($obj);
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
	
	public function getTemplateHeaderItems() {
		$items = array();
		$h = Loader::helper("html");
		$dh = Loader::helper('file');
		if ($this->checkHeaderItems == false) {
			return $items;
		} else {
			foreach($this->itemsToCheck as $t => $i) {
				if (file_exists($this->basePath . '/' . $i)) {
					switch($t) {
						case 'CSS':
							$items[] = $h->css($this->getBaseURL() . '/' . $i);
							break;
						case 'JAVASCRIPT':
							$items[] = $h->javascript($this->getBaseURL() . '/' . $i);
							break;
					}
				}
			}
			$css = $dh->getDirectoryContents($this->basePath . '/' . DIRNAME_CSS);
			$js = $dh->getDirectoryContents($this->basePath . '/' . DIRNAME_JAVASCRIPT);
			if (count($css) > 0) {
				foreach($css as $i) {
					if(substr($i,-4)=='.css') {
						$items[] = $h->css($this->getBaseURL() . '/' . DIRNAME_CSS . '/' . $i);
					}
				}
			}
			if (count($js) > 0) {
				foreach($js as $i) {
					if (substr($i,-3)=='.js') {
						$items[] = $h->javascript($this->getBaseURL() . '/' . DIRNAME_JAVASCRIPT . '/' . $i);
					}
				}
			}
			return $items;
		}
	}



}
	