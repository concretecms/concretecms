<?php
namespace Concrete\Attribute\Select;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\SelectValueOption;
use Gettext\Translations;
use Loader;
use \Concrete\Core\Foundation\Object;

class Option extends Object
{

    public static function getByID($avSelectOptionID)
    {
        $orm = \Database::connection()->getEntityManager();
        $r = $orm->getRepository('\Concrete\Core\Entity\Attribute\Value\SelectValueOption');
        $o = $r->findOneBy(array('avSelectOptionID' => $avSelectOptionID));
        return $o;
    }

    public static function getByValue($value, $ak = false)
    {
        $orm = \Database::connection()->getEntityManager();
        $r = $orm->getRepository('\Concrete\Core\Entity\Attribute\Value\SelectValueOption');
        $o = $r->findOneByValue($value);
        if (!$ak) {
            return $o;
        } else {
            $key = $o->getAttributeKey();
            if (is_object($key) && $key->getAttributeKeyID() == $ak->getAttributeKeyID()) {
                return $o;
            }
        }
    }


    public static function add(Key $key, $value, $isEndUserAdded = false)
    {
        $option = new SelectValueOption();
        $option->setIsEndUserAdded($isEndUserAdded);
        $option->setValue($value);
        $option->setAttributeKey($key);
        return $option;
    }


}