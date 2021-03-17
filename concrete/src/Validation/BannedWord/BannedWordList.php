<?php
namespace Concrete\Core\Validation\BannedWord;

use Concrete\Core\Legacy\DatabaseItemList;

/**
 * @deprecated This class will be removed from version 9.
 */
class BannedWordList extends DatabaseItemList
{
    public function __construct()
    {
        $this->setQuery('select * from BannedWords');
        $this->sortBy('bwID', 'asc');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $r = parent::get($itemsToGet, $offset);
        $bannedwords = array();
        foreach ($r as $row) {
            $bannedwords[] = BannedWord::getByID($row['bwID']);
        }

        return $bannedwords;
    }
}
