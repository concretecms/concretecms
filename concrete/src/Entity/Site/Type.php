<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Entity\PackageTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteTypes")
 */
class Type
{

    const DEFAULT_TYPE_HANDLE = 'default';

    use PackageTrait;

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
     * @ORM\Column(type="integer")
     */
    protected $siteTypeThemeID = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $siteTypeHomePageTemplateID = 0;

    /**
     * @ORM\OneToMany(targetEntity="Site", cascade={"remove"}, mappedBy="type")
     */
    protected $sites;

    public function isDefault()
    {
        return $this->getSiteTypeHandle() == self::DEFAULT_TYPE_HANDLE;
    }

    /**
     * @param mixed $siteTypeID
     */
    public function getSiteTypeID()
    {
        return $this->siteTypeID;
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

    /**
     * @return mixed
     */
    public function getSiteTypeThemeID()
    {
        return $this->siteTypeThemeID;
    }

    /**
     * @param mixed $siteTypeThemeID
     */
    public function setSiteTypeThemeID($siteTypeThemeID)
    {
        $this->siteTypeThemeID = $siteTypeThemeID;
    }

    /**
     * @return mixed
     */
    public function getSiteTypeHomePageTemplateID()
    {
        return $this->siteTypeHomePageTemplateID;
    }

    /**
     * @param mixed $siteTypeHomePageTemplate
     */
    public function setSiteTypeHomePageTemplateID($siteTypeHomePageTemplateID)
    {
        $this->siteTypeHomePageTemplateID = $siteTypeHomePageTemplateID;
    }



}
