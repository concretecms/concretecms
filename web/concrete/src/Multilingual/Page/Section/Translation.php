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
            'msgstr' => $msgstr,
        ), array('mtID' => $this->mtID));
        $this->setTranslation($msgstr);
    }

    public static function getByRow($row)
    {
        $result = null;
        if ($row && $row['mtID']) {
            $result = new Translation($row['context'], $row['msgid'], $row['msgidPlural']);
            $result->mtID = $row['mtID'];
            $result->mtSectionID = $row['mtSectionID'];
            $result->setTranslation($row['msgstr']);
            if (isset($row['comments'])) {
                foreach (explode("\n", $row['comments']) as $comment) {
                    $result->addExtractedComment($comment);
                }
            }
            if ($result->hasPlural() && isset($row['msgstrPlurals'])) {
                foreach (explode("\x00", $row['msgstrPlurals']) as $pluralIndex => $plural) {
                    $result->setPluralTranslation($plural, $pluralIndex);
                }
            }
            if (isset($row['reference'])) {
                foreach (explode("\n", $row['reference']) as $reference) {
                    if ($reference !== '') {
                        $line = null;
                        $p = strrpos($reference, ':');
                        if ($p) {
                            $s = substr($reference, $p + 1);
                            if (preg_match('/^\d+$/', $s)) {
                                $line = (int) $s;
                                $reference = substr($reference, 0, $p);
                            }
                        }
                        $result->addReference($reference, $line);
                    }
                }
            }
            if (isset($row['flags'])) {
                foreach (explode("\n", $row['flags']) as $flag) {
                    if ($flag !== '') {
                        $result->addFlag($flag);
                    }
                }
            }
        }

        return $result;
    }

    public static function getByID($mtID)
    {
        $result = null;
        $db = \Database::get();
        $r = $db->query('select  * from MultilingualTranslations where mtID = ?', array($mtID));
        $row = $r->fetch();

        return self::getByRow($row);
    }
}
