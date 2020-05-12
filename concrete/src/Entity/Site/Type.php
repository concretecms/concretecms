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
    use PackageTrait;

    public const DEFAULT_TYPE_HANDLE = 'default';

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null
     */
    protected $siteTypeID;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $siteTypeHandle = '';

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $siteTypeName = '';

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $siteTypeThemeID = 0;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $siteTypeHomePageTemplateID = 0;

    /**
     * @ORM\OneToMany(targetEntity="Site", cascade={"remove"}, mappedBy="type")
     *
     * @var \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Site\Site[]
     */
    protected $sites;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->getSiteTypeHandle() === self::DEFAULT_TYPE_HANDLE;
    }

    /**
     * @return int|null NULL if not yet persisted
     */
    public function getSiteTypeID()
    {
        return $this->siteTypeID;
    }

    /**
     * @return string
     */
    public function getSiteTypeHandle()
    {
        return $this->siteTypeHandle;
    }

    /**
     * @param string $siteTypeHandle
     */
    public function setSiteTypeHandle($siteTypeHandle)
    {
        $this->siteTypeHandle = (string) $siteTypeHandle;
    }

    /**
     * @return string
     */
    public function getSiteTypeName()
    {
        return $this->siteTypeName;
    }

    /**
     * @param string $siteTypeName
     */
    public function setSiteTypeName($siteTypeName)
    {
        $this->siteTypeName = (string) $siteTypeName;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Site\Site[]
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @return int
     */
    public function getSiteTypeThemeID()
    {
        return $this->siteTypeThemeID;
    }

    /**
     * @param int $siteTypeThemeID
     */
    public function setSiteTypeThemeID($siteTypeThemeID)
    {
        $this->siteTypeThemeID = (int) $siteTypeThemeID;
    }

    /**
     * @return int
     */
    public function getSiteTypeHomePageTemplateID()
    {
        return $this->siteTypeHomePageTemplateID;
    }

    /**
     * @param int $siteTypeHomePageTemplateID
     */
    public function setSiteTypeHomePageTemplateID($siteTypeHomePageTemplateID)
    {
        $this->siteTypeHomePageTemplateID = (int) $siteTypeHomePageTemplateID;
    }
}
