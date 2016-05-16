<?php
namespace Concrete\Core\File\Set;

use Concrete\Core\Entity\File\File as FileEntity;
use FileSet;
use Loader;

class File
{
    public static function getFileSetFiles(Set $fs)
    {
        $db = Loader::db();
        $r = $db->query('SELECT fsfID FROM FileSetFiles WHERE fsID = ? ORDER BY fsDisplayOrder ASC', array($fs->getFileSetID()));
        $files = array();
        while ($row = $r->FetchRow()) {
            $fsf = static::getByID($row['fsfID']);
            if (is_object($fsf)) {
                $files[] = $fsf;
            }
        }

        return $files;
    }

    public static function getByID($fsfID)
    {
        $db = Loader::db();
        $r = $db->GetRow('SELECT * FROM FileSetFiles WHERE fsfID = ?', array($fsfID));
        if (is_array($r) && $r['fsfID']) {
            $fsf = new static();
            $fsf = array_to_object($fsf, $r);

            return $fsf;
        }
    }

    public static function createAndGetFile($f_id, $fs_id)
    {
        $db = Loader::db();
        $fsfID = $db->GetOne('SELECT fsfID FROM FileSetFiles WHERE fID = ? AND fsID = ?', array($f_id, $fs_id));
        if ($fsfID > 0) {
            return static::getByID($fsfID);
        } else {
            $fs = FileSet::getByID($fs_id);
            $f = \Concrete\Core\File\File::getByID($f_id);
            $fsf = static::add($f, $fs);

            return $fsf;
        }
    }

    public static function add(FileEntity $f, FileSet $fs)
    {
        $db = Loader::db();
        $fsDisplayOrder = $db->GetOne('SELECT count(fID) FROM FileSetFiles WHERE fsID = ?', array($fs->getFileSetID()));
        $db->insert(
            'FileSetFiles',
            array(
                'fsID' => $fs->getFileSetID(),
                'timestamp' => date('Y-m-d H:i:s'),
                'fID' => $f->getFileID(),
                'fsDisplayOrder' => $fsDisplayOrder, ));
        $fsfID = $db->lastInsertId();

        return self::getByID($fsfID);
    }
}
