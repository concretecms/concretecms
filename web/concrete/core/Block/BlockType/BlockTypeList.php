<?
namespace Concrete\Core\Block\BlockType;
use \Concrete\Core\Foundation\Object;
use Loader;
use Environment;
use CacheLocal;
use BlockType as ConcreteBlockType;
use Package;
use \Concrete\Core\Foundation\Collection\Database\DatabaseItemList;

class BlockTypeList extends DatabaseItemList {

	protected $autoSortColumns = array('btHandle', 'btID', 'btDisplayOrder');
	
	function __construct() {
		$this->setQuery("select btID from BlockTypes");
		$this->sortByMultiple('btDisplayOrder asc', 'btName asc', 'btID asc');
	}

	public function get($itemsToGet = 100, $offset = 0) {
		$r = parent::get( $itemsToGet, intval($offset));
		$blocktypes = array();
		foreach($r as $row) {
			$bt = ConcreteBlockType::getByID($row['btID']);			
			if (is_object($bt)) {
				$blocktypes[] = $bt;
			}
		}
		return $blocktypes;
	}
	
	public function filterByPackage(Package $pkg) {
		$this->filter('pkgID', $pkg->getPackageID());
	}
	
	/**
	 * @todo comment this one
	 * @param string $xml
	 * @return void
	 */
	public static function exportList($xml) {
		$btl = new static();
		$blocktypes = $btl->get();
		$nxml = $xml->addChild('blocktypes');
		foreach($blocktypes as $bt) {
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
		$btl = new static();
		$btl->filter(false, 'btHandle like \'dashboard_%\'');
		$blockTypes = $btl->get();
		return $blockTypes;
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
		$btl = new static();
		return $btl->get();
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
