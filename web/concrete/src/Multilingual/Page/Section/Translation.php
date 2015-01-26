<?php

namespace Concrete\Core\Multilingual\Page\Section;

defined('C5_EXECUTE') or die("Access Denied.");

class Translation extends \Gettext\Translation
{

    public function getID()
    {
        return $this->mtID;
    }

    public function updateTranslation($msgstr)
    {
        $db = \Database::get();
        $db->update('MultilingualTranslations', array(
            'msgstr' => $msgstr
        ), array('mtID' => $this->mtID));
        $this->setTranslation($msgstr);
    }

    public static function getByID($mtID)
    {
        $db = \Database::get();
        $r = $db->query('select  * from MultilingualTranslations mt where mtID = ?', array($mtID));
        $row = $r->fetch();
        if ($row && $row['mtID']) {
            $t = new Translation();
            $t->mtID = $row['mtID'];
            $t->mtSectionID = $row['mtSectionID'];
            $t->setOriginal($row['msgid']);
            $t->setTranslation($row['msgstr']);
            $t->setContext($row['context']);

            return $t;
        }
    }

    public static function getByString($msgid)
    {
        $db = \Database::get();
        $r = $db->query('select mtID from MultilingualTranslations mt where msgid = ?', array($msgid));
        $row = $r->fetch();
        if ($row && $row['mtID']) {
            return static::getByID($row['mtID']);
        }
    }


}