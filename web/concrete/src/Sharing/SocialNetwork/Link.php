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
     * The social service handle
     * @Column(type="string")
     */
    protected $ssHandle;

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

    public function setServiceHandle($ssHandle)
    {
        $this->ssHandle = $ssHandle;
    }

    public function getServiceHandle()
    {
        return $this->ssHandle;
    }

    public function getServiceIconHTML()
    {
        $service = $this->getServiceObject();
        return $service->getServiceIconHTML();
    }

    public function getServiceObject()
    {
        return Service::getByHandle($this->ssHandle);
    }

    public static function getList()
    {
        $em = \ORM::entityManager('core');
        return $em->getRepository('\Concrete\Core\Sharing\SocialNetwork\Link')->findBy(array(), array('ssHandle' => 'asc'));
    }

    public function save()
    {
        $em = \ORM::entityManager('core');
        $em->persist($this);
        $em->flush();
    }

    public static function exportList($node)
    {
        $child = $node->addChild('sociallinks');
        $list = static::getList();
        foreach($list as $link) {
            $linkNode = $child->addChild('link');
            $linkNode->addAttribute('service', $link->getServiceObject()->getHandle());
            $linkNode->addAttribute('url', $link->getURL());

        }
    }

    public function delete()
    {
        $em = \ORM::entityManager('core');
        $em->remove($this);
        $em->flush();
    }

    public static function getByID($id)
    {
        $em = \ORM::entityManager('core');
        $r = $em->find('\Concrete\Core\Sharing\SocialNetwork\Link', $id);
        return $r;
    }

    public static function getByServiceHandle($ssHandle)
    {
        $em = \ORM::entityManager('core');
        return $em->getRepository('\Concrete\Core\Sharing\SocialNetwork\Link')->findOneBy(
            array('ssHandle' => $ssHandle)
        );
    }

}