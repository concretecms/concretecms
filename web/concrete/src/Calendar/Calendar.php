<?php
namespace Concrete\Core\Calendar;
use Database;

/**
 * @Entity
 * @Table(name="Calendars")
 */
class Calendar
{
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->caName;
    }

    /**
     * @param mixed $caName
     */
    public function setName($caName)
    {
        $this->caName = $caName;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->caID;
    }

    /**
     * @param mixed $caID
     */
    public function setID($caID)
    {
        $this->caID = $caID;
    }

    /**
     * @Column(type="string")
     */
    protected $caName;

    /**
     * @Id @Column(columnDefinition="integer unsigned")
     * @GeneratedValue
     */
    protected $caID;

    public static function getList()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\Calendar\Calendar')->findBy(array(), array('caName' => 'asc'));
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = Database::get()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\Calendar\Calendar', $id);
        return $r;
    }
}
