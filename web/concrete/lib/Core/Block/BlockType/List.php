<?
namespace Concrete\Core\Block\BlockType;
use \Concrete\Core\Foundation\Object;
use Loader;
use Environment;
use CacheLocal;

class List extends Object {

	/**
	 * array of BlockType objects - should likely be considered a protected property
	 * use getBlockTypeList instead of accessing this property directly
	 * @see BlockTypeList::getBlockTypeList()
	 * @var BlockType[] $btArray
	 */
	public $btArray = array();
	
	/**
	 * Gets an array of BlockTypes for a given Package
	 * @param Package $pkg
	 * @return BlockType[]
	 */
	public static function getByPackage($pkg) {
		$db = Loader::db();
		$r = $db->Execute("select btID from BlockTypes where pkgID = ?", $pkg->getPackageID());
		$blockTypes = array();
		while ($row = $r->FetchRow()) {
			$blockTypes[] = BlockType::getByID($row['btID']);
		}
		return $blockTypes;
	}
	
	
	/**
	 * @todo comment this one
	 * @param string $xml
	 * @return void
	 */
	public static function exportList($xml) {
		$attribs = self::getInstalledList();
		$nxml = $xml->addChild('blocktypes');
		foreach($attribs as $bt) {
			$type = $nxml->addChild('blocktype');
			$type->addAttribute('handle', $bt->getBlockTypeHandle());
			$type->addAttribute('package', $bt->getPackageHandle());
		}
	}

	/**
	 * returns an array of Block Types used in the concrete5 Dashboard
	 * @return BlockType[]
	 */
	public static function getDashboardBlockTypes() {
		$db = Loader::db();
		$btIDs = $db->GetCol('select btID from BlockTypes where btHandle like "dashboard_%" order by btDisplayOrder asc, btID asc');
		$blockTypes = array();
		foreach($btIDs as $btID) {
			$blockTypes[] = BlockType::getByID($btID);
		}
		return $blockTypes;
	}
	
	/**
	 * BlockTypeList class constructor
	 * @param array $allowedBlocks array of allowed BlockType id's if you'd like to limit the list to just those
	 * @return BlockTypeList
	 */
	function __construct($allowedBlocks = null) {
		$db = Loader::db();
		$this->btArray = array();
					
		$q = "select btID from BlockTypes where btIsInternal = 0 ";
		if ($allowedBlocks != null) {
			$q .= ' and btID in (' . implode(',', $allowedBlocks) . ') ';
		}
		$q .= ' order by btDisplayOrder asc, btName asc, btID asc';
		
		$r = $db->query($q);

		if ($r) {
			while ($row = $r->fetchRow()) {
				$bt = BlockType::getByID($row['btID']);
				if (is_object($bt)) {
					$this->btArray[] = $bt;
				}
			}
			$r->free();
		}
										
		return $this;
	}
	
	/**
	 * gets the array of BlockType objects
	 * @return BlockType[]
	 * @see BlockTypeList::getInstalledList()
	 */
	public function getBlockTypeList() {
		return $this->btArray;
	}

	/**
	 * Gets a list of block types that are not installed, used to get blocks that can be installed
	 * This function only surveys the web/blocks directory - it's not looking at the package level.
	 * @return BlockType[] 
	 */
	public static function getAvailableList() {
		$blocktypes = array();
		$dir = DIR_FILES_BLOCK_TYPES;
		$db = Loader::db();
		
		$btHandles = $db->GetCol("select btHandle from BlockTypes order by btDisplayOrder asc, btName asc, btID asc");
		
		$aDir = array();
		if (is_dir($dir)) {
			$handle = opendir($dir);
			while(($file = readdir($handle)) !== false) {
				if (strpos($file, '.') === false) {
					$fdir = $dir . '/' . $file;
					if (is_dir($fdir) && !in_array($file, $btHandles) && file_exists($fdir . '/' . FILENAME_BLOCK_CONTROLLER)) {
						$bt = new BlockType;
						$bt->btHandle = $file;
						$class = $bt->getBlockTypeClassFromHandle($file);
						
						require_once($fdir . '/' . FILENAME_BLOCK_CONTROLLER);
						if (!class_exists($class)) {
							continue;
						}
						$bta = new $class;
						$bt->btName = $bta->getBlockTypeName();
						$bt->btDescription = $bta->getBlockTypeDescription();
						$bt->hasCustomViewTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_VIEW);
						$bt->hasCustomEditTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_EDIT);
						$bt->hasCustomAddTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_ADD);
						
						
						$btID = $db->GetOne("select btID from BlockTypes where btHandle = ?", array($file));
						$bt->installed = ($btID > 0);
						$bt->btID = $btID;
						
						$blocktypes[] = $bt;
						
					}
				}				
			}
		}
		
		return $blocktypes;
	}

	/**
	 * gets a list of installed BlockTypes
	 * @return BlockType[]
	 */	
	public static function getInstalledList() {
		$db = Loader::db();
		$r = $db->query("select btID from BlockTypes order by btDisplayOrder asc, btName asc, btID asc");
		$btArray = array();
		while ($row = $r->fetchRow()) {
			$bt = BlockType::getByID($row['btID']);
			if (is_object($bt)) {
				$btArray[] = $bt;
			}
		}
		return $btArray;
	}
	
	/**
	 * Gets a list of installed BlockTypes 
	 * - could be defined as static
	 * @todo we have three duplicate functions getBlockTypeArray, getInstalledList, getBlockTypeList
	 * @return BlockType[]
	 */	
	public function getBlockTypeArray() {
		$db = Loader::db();
		$q = "select btID from BlockTypes order by btDisplayOrder asc, btName asc, btID asc";
		$r = $db->query($q);
		$btArray = array();
		if ($r) {
			while ($row = $r->fetchRow()) {
				$bt = BlockType::getByID($row['btID']);
				if (is_object($bt)) {
					$btArray[] = $bt;
				}
			}
			$r->free();
		}
		return $btArray;
	}
	
	public static function resetBlockTypeDisplayOrder($column = 'btID') {
		$db = Loader::db();
		$ca = new Cache();
		$stmt = $db->Prepare("UPDATE BlockTypes SET btDisplayOrder = ? WHERE btID = ?");
		$btDisplayOrder = 1;
		$blockTypes = $db->GetArray("SELECT btID, btHandle, btIsInternal FROM BlockTypes ORDER BY {$column} ASC");
		foreach ($blockTypes as $bt) {
			if ($bt['btIsInternal']) {
				$db->Execute($stmt, array(0, $bt['btID']));
			} else {
				$db->Execute($stmt, array($btDisplayOrder, $bt['btID']));
				$btDisplayOrder++;
			}
			$ca->delete('blockTypeByID', $bt['btID']);
			$ca->delete('blockTypeByHandle', $bt['btHandle']);
		}
		$ca->delete('blockTypeList', false);
	}
	
}
