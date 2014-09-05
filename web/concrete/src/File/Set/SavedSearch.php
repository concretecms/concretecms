<?php
namespace Concrete\Core\File\Set;

use Loader;

class SavedSearch extends Set
{

    public static function add($name, $searchRequest, $searchColumnsObject)
    {
        $fs = parent::createAndGetSet($name, FileSet::TYPE_SAVED_SEARCH);
        $db = Loader::db();
        $v = array($fs->getFileSetID(), serialize($searchRequest), serialize($searchColumnsObject));
        $db->Execute('INSERT INTO FileSetSavedSearches (fsID, fsSearchRequest, fsResultColumns) VALUES (?, ?, ?)', $v);
        return $fs;
    }

}


