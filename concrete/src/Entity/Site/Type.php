<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteTypes")
 */
class Type
{


    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteTypeID;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $siteTypeHandle;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $siteTypeName;

    /**
     * @ORM\OneToMany(targetEntity="Site", cascade={"remove"}, mappedBy="type")
     */
    protected $sites;

    /**
     * @param mixed $siteTypeID
     */
    public function setSiteTypeID($siteTypeID)
    {
        $this->siteTypeID = $siteTypeID;
    }

    /**
     * @return mixed
     */
    public function getSiteTypeHandle()
    {
        return $this->siteTypeHandle;
    }

    /**
     * @param mixed $siteTypeHandle
     */
    public function setSiteTypeHandle($siteTypeHandle)
    {
        $this->siteTypeHandle = $siteTypeHandle;
    }

    /**
     * @return mixed
     */
    public function getSiteTypeName()
    {
        return $this->siteTypeName;
    }

    /**
     * @param mixed $siteTypeName
     */
    public function setSiteTypeName($siteTypeName)
    {
        $this->siteTypeName = $siteTypeName;
    }

    /**
     * @return mixed
     */
    public function getSites()
    {
        return $this->sites;
    }

    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }


}
