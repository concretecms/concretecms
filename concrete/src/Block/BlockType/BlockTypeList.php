<?php
namespace Concrete\Core\Block\BlockType;

use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Support\Facade\Application;
use Core;
use Loader;
use Package;

use Concrete\Core\Legacy\DatabaseItemList;

class BlockTypeList extends DatabaseItemList
{
    protected $autoSortColumns = array('btHandle', 'btID', 'btDisplayOrder');
    protected $includeInternalBlockTypes = false;

    public function __construct()
    {
        $this->setQuery("select btID from BlockTypes");
        $this->sortByMultiple('btDisplayOrder asc', 'btName asc', 'btID asc');
    }
    public function includeInternalBlockTypes()
    {
        $this->includeInternalBlockTypes = true;
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        if (!$this->includeInternalBlockTypes) {
            $this->filter('btIsInternal', false);
        }
        $r = parent::get($itemsToGet, intval($offset));
        $blocktypes = array();
        foreach ($r as $row) {
            $bt = BlockType::getByID($row['btID']);
            if (is_object($bt)) {
                $blocktypes[] = $bt;
            }
        }

        return $blocktypes;
    }

    public function filterByPackage($pkg)
    {
        $this->filter('pkgID', $pkg->getPackageID());
    }

    /**
     * @todo comment this one
     *
     * @param string $xml
     */
    public static function exportList($xml)
    {
        $btl = new static();
        $blocktypes = $btl->get();
        $nxml = $xml->addChild('blocktypes');
        foreach ($blocktypes as $bt) {
            $type = $nxml->addChild('blocktype');
            $type->addAttribute('handle', $bt->getBlockTypeHandle());
            $type->addAttribute('package', $bt->getPackageHandle());
        }
    }

    /**
     * Gets a list of block types that are not installed, used to get blocks that can be installed
     * This function only surveys the web/blocks directory - it's not looking at the package level.
     *
     * @return BlockType[]
     */
    public static function getAvailableList()
    {
        $blocktypes = array();
        $dir = DIR_FILES_BLOCK_TYPES;
        $db = Loader::db();

        $btHandles = $db->GetCol("select btHandle from BlockTypes order by btDisplayOrder asc, btName asc, btID asc");

        $aDir = array();
        if (is_dir($dir)) {
            $handle = opendir($dir);
            while (($file = readdir($handle)) !== false) {
                if (strpos($file, '.') === false) {
                    $fdir = $dir . '/' . $file;
                    if (is_dir($fdir) && !in_array($file, $btHandles) && file_exists($fdir . '/' . FILENAME_BLOCK_CONTROLLER)) {
                        $bt = BlockType::getByHandle($file);
                        if (!is_object($bt)) {
                            $bt = new BlockTypeEntity();
                            $bt->setBlockTypeHandle($file);
                            $class = $bt->getBlockTypeClass();
                            $bta = Application::getFacadeApplication()->build($class);
                            $bt->setBlockTypeName($bta->getBlockTypeName());
                            $bt->setBlockTypeDescription($bta->getBlockTypeDescription());
                            $bt->hasCustomViewTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_VIEW);
                            $bt->hasCustomEditTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_EDIT);
                            $bt->hasCustomAddTemplate = file_exists(DIR_FILES_BLOCK_TYPES . '/' . $file . '/' . FILENAME_BLOCK_ADD);
                            $bt->installed = false;
                        } else {
                            $bt->installed = true;
                        }

                        $blocktypes[] = $bt;
                    }
                }
            }
        }

        return $blocktypes;
    }

    /**
     * gets a list of installed BlockTypes.
     *
     * @return BlockType[]
     */
    public static function getInstalledList()
    {
        $btl = new static();

        return $btl->get();
    }

    public static function resetBlockTypeDisplayOrder($column = 'btID')
    {
        $db = Loader::db();
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache');
        $stmt = $db->Prepare("UPDATE BlockTypes SET btDisplayOrder = ? WHERE btID = ?");
        $btDisplayOrder = 1;
        $blockTypes = $db->GetArray("SELECT btID, btHandle, btIsInternal FROM BlockTypes ORDER BY {$column} ASC");
        foreach ($blockTypes as $bt) {
            if ($bt['btIsInternal']) {
                $db->Execute($stmt, array(0, $bt['btID']));
            } else {
                $db->Execute($stmt, array($btDisplayOrder, $bt['btID']));
                ++$btDisplayOrder;
            }
            $cache->delete('blockTypeByID/' .$bt['btID']);
            $cache->delete('blockTypeByHandle/' . $bt['btHandle']);
        }
        $cache->delete('blockTypeList');
    }
}
