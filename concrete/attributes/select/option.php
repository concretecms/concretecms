<?php
namespace Concrete\Attribute\Select;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption;
use Gettext\Translations;


/**
 * @deprecated
 */
class Option
{

    /**
     * @param Key $ak
     * @param $option
     * @param int $isEndUserAdded
     * @return Option
     */
    public static function add($ak, $value, $isEndUserAdded = 0)
    {
        /**
         * @var $type SelectSettings
         */
        $type = $ak->getAttributeKeySettings();
        $list = $type->getOptionList();

        $option = new SelectValueOption();
        $option->setSelectAttributeOptionValue($value);
        $option->setIsEndUserAdded($isEndUserAdded);
        $option->setOptionList($list);

        $em = \Database::connection()->getEntityManager();
        $em->persist($option);
        $em->flush();

        return $option;
    }

    public static function getByID($id)
    {
        $em = \Database::connection()->getEntityManager();
        return $em->find('\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption', $id);
    }


    public static function getByValue($value, $ak = false)
    {
        $em = \Database::connection()->getEntityManager();
        $controller = new \Concrete\Attribute\Select\Controller($em);
        return $controller->getOptionByValue($value, $ak);
    }

    public static function exportTranslations()
    {
        $translations = new Translations();
        $em = \Database::connection()->getEntityManager();
        $options = $em->getRepository(SelectValueOption::class)->findAll();
        /**
         * @var $option SelectValueOption
         */
        foreach($options as $option) {
            $translations->insert('SelectAttributeValue', $option->getSelectAttributeOptionValue());
        }
        return $translations;
    }


}