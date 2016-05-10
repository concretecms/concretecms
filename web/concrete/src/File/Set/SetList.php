<?php
namespace Concrete\Core\File\Set;

use Concrete\Core\Legacy\DatabaseItemList;
use FileSet;
use Loader;
use User;

class SetList extends DatabaseItemList
{
    protected $itemsPerPage = 10;

    public function __construct()
    {
        $this->setQuery("SELECT FileSets.fsID FROM FileSets");
        $this->sortBy('fsName', 'asc');
    }

    public function filterByKeywords($kw)
    {
        $db = Loader::db();
        $this->filter(false, "(FileSets.fsName like " . $db->qstr('%' . $kw . '%') . ")");
    }

    public function filterByType($fsType)
    {
        switch ($fsType) {
            case FileSet::TYPE_PRIVATE:
                $u = new User();
                $this->filter('FileSets.uID', $u->getUserID());
                break;
        }
        $this->filter('FileSets.fsType', $fsType);
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $sets = array();
        $r = parent::get($itemsToGet, $offset);
        foreach ($r as $row) {
            $fs = FileSet::getByID($row['fsID']);
            if (is_object($fs)) {
                $sets[] = $fs;
            }
        }

        return $sets;
    }
}
