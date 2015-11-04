<?php

namespace Concrete\Core\File\Set;

use Database;
use FileSet;

class SavedSearch extends Set
{
    public static function add($name, $searchRequest, $searchColumnsObject)
    {
        $fs = parent::createAndGetSet($name, FileSet::TYPE_SAVED_SEARCH);
        $db = Database::connection();
        $v = array($fs->getFileSetID(), serialize($searchRequest), serialize($searchColumnsObject));
        $db->executeQuery('INSERT INTO FileSetSavedSearches (fsID, fsSearchRequest, fsResultColumns) VALUES (?, ?, ?)', $v);

        return $fs;
    }
}
