<?php
namespace Concrete\Core\StyleCustomizer;

use Database;

/**
 * @Entity
 * @Table(name="StyleCustomizerCustomCssRecords")
 */
class CustomCssRecord
{

    /**
     * @Column(type="text")
     */
    protected $value;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $sccRecordID;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRecordID()
    {
        return $this->sccRecordID;
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\StyleCustomizer\CustomCssRecord', $id);
        return $r;
    }

}