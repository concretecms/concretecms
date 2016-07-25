<?php
namespace Concrete\Core\Entity\Sharing\SocialNetwork;

use Concrete\Core\Sharing\SocialNetwork\Service;
use Database;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SocialLinks")
 */
class Link
{
    /**
     * The social service handle.
     *
     * @ORM\Column(type="string")
     */
    protected $ssHandle;

    /**
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     **/
    protected $site;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
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

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
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

    public function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }
}
