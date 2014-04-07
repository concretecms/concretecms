<?
namespace Concrete\Core\Block\BlockType;
use Loader;
use Environment;
use Localization;
use CacheLocal;
use Package;
use Cache;
use Database as DB;
use User;
use Block;
use \Concrete\Core\Filesystem\TemplateFile;

class BlockType {

	protected $btID;
	protected $btHandle;
	protected $btName;
	protected $btDescription;
	protected $btCopyWhenPropagate;
	protected $btIncludeAll;
	protected $btIsInternal;
	protected $btSupportsInlineEdit;
	protected $btSupportsInlineAdd;
	protected $btInterfaceHeight;
	protected $btInterfaceWidth;
	protected $pkgID;
	protected $controller;
	/** 
	 * Sets the block type handle
	 */
	public function setBlockTypeHandle($btHandle) {
		$this->btHandle = $btHandle;
	}

	/**
	 * Determines if the block type has templates available
	 * @return boolean
	 */
	public function hasAddTemplate() {
		$bv = new BlockView($this);
		$path = $bv->getBlockPath(FILENAME_BLOCK_ADD);
		if (file_exists($path . '/' . FILENAME_BLOCK_ADD)) {
			return true;
		}
		return false;
	}

	/** 
	 * Retrieves a BlockType object based on its btHandle
	 * @return BlockType
	 */
	public static function getByHandle($btHandle) {
		$em = DB::get()->getEntityManager();
		$bt = $em->getRepository('\Concrete\Core\Block\BlockType\BlockType')->findOneBy(array('btHandle' => $btHandle));
		$bt->loadController();
		return $bt;
	}

	/** 
	 * Retrieves a BlockType object based on its btID
	 * @return BlockType
	 */
	public static function getByID($btID) {
		$em = DB::get()->getEntityManager();
		$bt = $em->getRepository('\Concrete\Core\Block\BlockType\BlockType')->find($btID);
		$bt->loadController();
		return $bt;
	}

	/** 
	 * Loads controller
	 */
	protected function loadController() {
		if (!isset($this->controller)) {
			$class = static::getBlockTypeMappedClass($this->getBlockTypeHandle());
			$this->controller = new $class($this);
		}
	}

	/** 
	 * if a the current BlockType is Internal or not - meaning one of the core built-in concrete5 blocks
	 * @access private
	 * @return boolean
	 */
	function isBlockTypeInternal() {return $this->btIsInternal;}

	/** 
	 * if a the current BlockType supports inline edit or not
	 * @return boolean
	 */
	public function supportsInlineEdit() {return $this->btSupportsInlineEdit;}

	/** 
	 * if a the current BlockType supports inline add or not
	 * @return boolean
	 */
	public function supportsInlineAdd() {return $this->btSupportsInlineAdd;}
	
	/** 
	 * Returns true if the block type is internal (and therefore cannot be removed) a core block
	 * @return boolean
	 */
	public function isInternalBlockType() {
		return $this->btIsInternal;
	}

	/**
	 * returns the width in pixels that the block type's editing dialog will open in
	 * @return int
	 */
	public function getBlockTypeInterfaceWidth() {return $this->btInterfaceWidth;}
	
	/**
	 * returns the height in pixels that the block type's editing dialog will open in
	 * @return int
	 */
	public function getBlockTypeInterfaceHeight() {return $this->btInterfaceHeight;}
	
	/**
	 * returns the id of the BlockType's package if it's in a package
	 * @return int
	 */
	public function getPackageID() {return $this->pkgID;}
	
	/**
	 * returns the handle of the BlockType's package if it's in a package
	 * @return string
	 */
	public function getPackageHandle() {
		return \Concrete\Core\Package\PackageList::getHandle($this->pkgID);
	}

	/**
	 * gets the BlockTypes description text
	 * @return string
	 */
	public function getBlockTypeDescription() {
		return $this->btDescription;
	}

	/** 
	 * @return int
	 */
	public function getBlockTypeID() {
		return $this->btID;
	}
	
	/** 
	 * @return string
	 */
	public function getBlockTypeHandle() {
		return $this->btHandle;
	}

	/** 
	 * @return string
	 */
	public function getBlockTypeName() {
		return $this->btName;
	}
	
	/** 
	 * @return boolean
	 */
	public function isCopiedWhenPropagated() {
		return $this->btCopyWhenPropagate;
	}

	/** 
	 * If true, this block is not versioned on a page – it is included as is on all versions of the page, even when updated.
	 * @return boolean
	 */
	public function includeAll() {
		return $this->btIncludeAll;
	}

	/** 
	 * Returns the class for the current block type.
	 */
	public function getBlockTypeClass() {
		return static::getBlockTypeMappedClass($this->btHandle);
	}

	/**
	 * @deprecated
	 */
	public function getBlockTypeClassFromHandle() {
		return $this->getBlockTypeClass();
	}
	

	/** 
	 * Return the class file that this BlockType uses
	 * @return string
	 */
	public static function getBlockTypeMappedClass($btHandle) {
		$txt = Loader::helper('text');
		$className = \Concrete\Core\Foundation\ClassLoader::getClassName('Block\\' . $txt->camelcase($btHandle) . '\\Controller');
		return $className;
	}
	
	/** 
	 * Returns an array of all BlockTypeSet objects that this block is in
	 */
	public function getBlockTypeSets() {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select btsID from BlockTypeSetBlockTypes where btID = ? order by displayOrder asc', array($this->getBlockTypeID()));
		while ($row = $r->FetchRow()) {
			$list[] = BlockTypeSet::getByID($row['btsID']);
		}
		$r->Close();
		return $list;
	}

	/** 
	 * Returns the number of unique instances of this block throughout the entire site
	 * note - this count could include blocks in areas that are no longer rendered by the theme
	 * @param boolean specify true if you only want to see the number of blocks in active pages
	 * @return int
	 */
	public function getCount($ignoreUnapprovedVersions = false) {
		$db = Loader::db();
		if ($ignoreUnapprovedVersions) {
    		$count = $db->GetOne("SELECT count(btID) FROM Blocks b
        			WHERE btID=?
        			AND EXISTS (
            			SELECT 1 FROM CollectionVersionBlocks cvb 
            			INNER JOIN CollectionVersions cv ON cv.cID=cvb.cID AND cv.cvID=cvb.cvID
            			WHERE b.bID=cvb.bID AND cv.cvIsApproved=1
        			)", array($this->btID));            
		} else {
    		$count = $db->GetOne("SELECT count(btID) FROM Blocks WHERE btID = ?", array($this->btID));
		}
		return $count;
	}

	/**
	 * Not a permissions call. Actually checks to see whether this block is not an internal one.
	 * @return boolean
	 */
	public function canUnInstall() {
		return (!$this->isBlockTypeInternal());
	}

	/** 
	 * Renders a particular view of a block type, using the public $controller variable as the block type's controller
	 * @param string template 'view' for the default
	 * @return void
	 */
	public function render($view = 'view') {
		$bv = new BlockView($this);
		$bv->render($view);
	}			

	/**
	 * get's the block type controller
	 * @return BlockTypeController
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * Gets the custom templates available for the current BlockType
	 * @return TemplateFile[]
	 */
	function getBlockTypeCustomTemplates() {
		$btHandle = $this->getBlockTypeHandle();
		$fh = Loader::helper('file');
		$files = array();
		$dir = DIR_FILES_BLOCK_TYPES . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES;
		if(is_dir($dir)) {
			$files = array_merge($files, $fh->getDirectoryContents($dir));
		}
		// NOW, we check to see if this btHandle has any custom templates that have been installed as separate packages
		foreach(PackageList::get()->getPackages() as $pkg) {
			$dir =
				(is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle()) ? DIR_PACKAGES : DIR_PACKAGES_CORE)
				. '/'. $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $btHandle . '/' . DIRNAME_BLOCK_TEMPLATES
			;
			if(is_dir($dir)) {
				$files = array_merge($files, $fh->getDirectoryContents($dir));
			}
		}
		$dir = DIR_FILES_BLOCK_TYPES_CORE . "/{$btHandle}/" . DIRNAME_BLOCK_TEMPLATES;
		if(is_dir($dir)) {
			$files = array_merge($files, $fh->getDirectoryContents($dir));
		}
		Loader::library('template_file');
		$templates = array();
		foreach(array_unique($files) as $file) {
			$templates[] = new TemplateFile($this, $file);
		}
		return TemplateFile::sortTemplateFileList($templates);
	}

	
	/** 
	 * @private
	 */
	function setBlockTypeDisplayOrder($displayOrder) {
		$db = Loader::db();
		
		$displayOrder = intval($displayOrder); //in case displayOrder came from a string (so ADODB escapes it properly)
		
		$sql = "UPDATE BlockTypes SET btDisplayOrder = btDisplayOrder - 1 WHERE btDisplayOrder > ?";
		$vals = array($this->btDisplayOrder);
		$db->Execute($sql, $vals);
		
		$sql = "UPDATE BlockTypes SET btDisplayOrder = btDisplayOrder + 1 WHERE btDisplayOrder >= ?";
		$vals = array($displayOrder);
		$db->Execute($sql, $vals);
		
		$sql = "UPDATE BlockTypes SET btDisplayOrder = ? WHERE btID = ?";
		$vals = array($displayOrder, $this->btID);
		$db->Execute($sql, $vals);
		
		// now we remove the block type from cache
		$ca = new Cache();
		$ca->delete('blockTypeByID', $this->btID);
		$ca->delete('blockTypeByHandle', $this->btHandle);
		$ca->delete('blockTypeList', false);
	}

	/** 
	 * @deprecated
	 */
	public static function installBlockTypeFromPackage($btHandle, $pkg) {
		static::installBlockType($btHandle, $pkg);
	}

	/**
	 * refreshes the BlockType's database schema throws an Exception if error
	 * @return void
	 */
	public function refresh() {
		if ($this->getPackageID() > 0) {
			$pkg = Package::getByID($this->getPackageID());
			$resp = BlockType::installBlockTypeFromPackage($this->getBlockTypeHandle(), $pkg, $this->getBlockTypeID());			
			if ($resp != '') {
				throw new Exception($resp);
			}
		} else {
			$resp = BlockType::installBlockType($this->getBlockTypeHandle(), $this->getBlockTypeID());			
			if ($resp != '') {
				throw new Exception($resp);
			}
		}
	}
	

	/** 
	 * Installs a BlockType that is passed via a btHandle string. The core or override directories are parsed.
	 */

	public static function installBlockType($btHandle, $pkg = false) {
		$env = Environment::get();
		$class = static::getBlockTypeMappedClass($btHandle);
		$bta = new $class;
		$path = dirname($env->getPath(DIRNAME_BLOCKS . '/' . helper('text')->camelcase($btHandle) . '/'. FILENAME_CONTROLLER));
		
		//Attempt to run the subclass methods (install schema from db.xml, etc.)
		$r = $bta->install($path);
	
		$currentLocale = Localization::activeLocale();
		if ($currentLocale != 'en_US') {
			// Prevent the database records being stored in wrong language
			Localization::changeLocale('en_US');
		}

		//Install the block
		$bt = new static();
		$bt->btHandle = $btHandle;
		if ($pkg instanceof Package) {
			$bt->pkgID = $pkg->getPackageID();
		} else {
			$bt->pkgID = 0;
		}
		$bt->btHandle = $btHandle;
		$bt->btName = $bta->getBlockTypeName();
		$bt->btDescription = $bta->getBlockTypeDescription();
		$bt->btCopyWhenPropagate = $bta->isCopiedWhenPropagated();
		$bt->btIncludeAll = $bta->includeAll();
		$bt->btIsInternal = $bta->isBlockTypeInternal();
		$bt->btSupportsInlineEdit = $bta->supportsInlineEdit();
		$bt->btSupportsInlineAdd = $bta->supportsInlineAdd();
		$bt->btInterfaceHeight = $bta->getInterfaceHeight();
		$bt->btInterfaceWidth = $bta->getInterfaceWidth();
		if ($currentLocale != 'en_US') {
			Localization::changeLocale($currentLocale);
		}

		$em = DB::get()->getEntityManager();
		$em->persist($bt);
		$em->flush();

		return $bt;
	}

    /** 
     * Removes the block type. Also removes instances of content.
     */
    public function delete() {
        $db = Loader::db();
        $r = $db->Execute('select cID, cvID, b.bID, arHandle from CollectionVersionBlocks cvb inner join Blocks b on b.bID = cvb.bID where btID = ?', array($this->getBlockTypeID()));
        while ($row = $r->FetchRow()) {
            $nc = Page::getByID($row['cID'], $row['cvID']);
            if(!is_object($nc) || $nc->isError()) continue;
            $b = Block::getByID($row['bID'], $nc, $row['arHandle']);
            if (is_object($b)) {
                $b->deleteBlock();
            }
        }

        $em = $db->getEntityManager();
        $em->remove($bt);
        $em->flush();  
        
        //Remove gaps in display order numbering (to avoid future sorting errors)
        BlockTypeList::resetBlockTypeDisplayOrder('btDisplayOrder');
    }

	/** 
	 * Adds a block to the system without adding it to a collection. 
	 * Passes page and area data along if it is available, however.
	 */
	public function add($data, $c = false, $a = false) {
		$db = Loader::db();
		
		$u = new User();
		if (isset($data['uID'])) {
			$uID = $data['uID'];
		} else { 
			$uID = $u->getUserID();
		}
		$btID = $this->btID;
		$dh = Loader::helper('date');
		$bDate = $dh->getSystemDateTime();
		$bIsActive = ($this->btActiveWhenAdded == 1) ? 1 : 0;
		
		$v = array($_POST['bName'], $bDate, $bDate, $bIsActive, $btID, $uID);
		$q = "insert into Blocks (bName, bDateAdded, bDateModified, bIsActive, btID, uID) values (?, ?, ?, ?, ?, ?)";
		
		$r = $db->prepare($q);
		$res = $db->execute($r, $v);

		$bIDnew = $db->Insert_ID();

		// we get the block object for the block we just added

		if ($res) {
			$nb = Block::getByID($bIDnew);

			$btHandle = $this->getBlockTypeHandle();
			
			$class = $this->getBlockTypeClass();
			if (is_object($c)) {
				$nb->setBlockCollectionObject($c);
			}
			$bc = new $class($nb);
			$bc->save($data);				
			return Block::getByID($bIDnew);
			
		}
		
	}
	

}