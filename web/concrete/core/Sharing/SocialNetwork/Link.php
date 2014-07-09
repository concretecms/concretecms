<?php
namespace Concrete\Core\Sharing\SocialNetwork;

use Database;

/**
 * @Entity
 * @Table(name="SocialLinks")
 */
class Link
{

    /**
     * The social service ID (for icon mapping)
     * @Column(type="integer")
     */
    protected $ssID;

    /**
     * @Column(type="string")
     */
    protected $url;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $slID;


    public function setURL($url)
    {
        $this->url = $url;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getID()
    {
        return $this->slID;
    }

    public function setServiceID($ssID)
    {
        $this->ssID = $ssID;
    }

    public function getServiceID()
    {
        return $this->ssID;
    }

    public function getServiceIconHTML()
    {
        $service = $this->getServiceObject();
        return $service->getServiceIconHTML();
    }

    public function getServiceObject()
    {
        return Service::getByID($this->ssID);
    }

    public static function getList()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\Sharing\SocialNetwork\Link')->findBy(array(), array('ssID' => 'asc'));
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
        $r = $em->find('\Concrete\Core\Sharing\SocialNetwork\Link', $id);
        return $r;
    }

}