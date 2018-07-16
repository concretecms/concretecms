<?php

namespace Concrete\Core\Entity\Site;

use Concrete\Core\Attribute\Category\SiteCategory;
use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\SiteValue;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Permission\Response\SiteResponse;
use Concrete\Core\Site\Config\Liaison;
use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sites")
 */
class Site implements TreeInterface, ObjectInterface, PermissionObjectInterface
{
    use ObjectTrait;

    /**
     * The site configuration repository.
     *
     * @var \Concrete\Core\Site\Config\Liaison
     */
    protected $siteConfig;

    /**
     * The site identifier.
     *
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $siteID;

    /**
     * The ID of the theme.
     *
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int
     */
    protected $pThemeID = 0;

    /**
     * Is this the default site?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $siteIsDefault = false;

    /**
     * The site handle.
     *
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $siteHandle;

    /**
     * The language sections of this site.
     *
     * @ORM\OneToMany(targetEntity="Locale", cascade={"all"}, mappedBy="site")
     * @ORM\JoinColumn(name="siteLocaleID", referencedColumnName="siteLocaleID")
     *
     * @var \Concrete\Core\Entity\Site\Locale[]|\Doctrine\Common\Collections\ArrayCollection
     **/
    protected $locales;

    /**
     * The site type.
     *
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="sites")
     * @ORM\JoinColumn(name="siteTypeID", referencedColumnName="siteTypeID")
     *
     * @var \Concrete\Core\Entity\Site\Type|null
     */
    protected $type;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Config\Repository\Repository $appConfigRepository The site configuration repository
     */
    public function __construct($appConfigRepository)
    {
        $this->updateSiteConfigRepository($appConfigRepository);
        $this->locales = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     *
     * @return int
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->siteID;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     *
     * @return string
     */
    public function getPermissionResponseClassName()
    {
        return SiteResponse::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     *
     * @return false
     */
    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     *
     * @return false
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     *
     * @return \Concrete\Core\Attribute\Category\SiteCategory
     */
    public function getObjectAttributeCategory()
    {
        $app = Application::getFacadeApplication();

        return $app->make(SiteCategory::class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttributeValueObject()
     *
     * @return \Concrete\Core\Entity\Attribute\Value\SiteValue|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        $result = null;
        if (!is_object($ak)) {
            $ak = SiteKey::getAttributeKeyByHandle($ak);
        }
        if ($ak !== null) {
            $result = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
            if ($result === null && $createIfNotExists) {
                $result = new SiteValue();
                $result->setSite($this);
                $result->setAttributeKey($ak);
            }
        }

        return $result;
    }

    /**
     * Set the site configuration repository.
     *
     * @param \Concrete\Core\Config\Repository\Repository $appConfigRepository
     */
    public function updateSiteConfigRepository($appConfigRepository)
    {
        $this->siteConfig = new Liaison($appConfigRepository, $this);
    }

    /**
     * Get the site configuration repository.
     *
     * @return \Concrete\Core\Site\Config\Liaison
     */
    public function getConfigRepository()
    {
        if (!$this->siteConfig) {
            $app = Application::getFacadeApplication();
            $this->updateSiteConfigRepository($app->make('config'), $this);
        }

        return $this->siteConfig;
    }

    /**
     * Get the site type object.
     *
     * @return \Concrete\Core\Entity\Site\Type|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the site type object.
     *
     * @param \Concrete\Core\Entity\Site\Type|null $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the language sections of this site.
     *
     * @return \Concrete\Core\Entity\Site\Locale[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Set the language sections of this site.
     *
     * @param \Concrete\Core\Entity\Site\Locale[]|\Doctrine\Common\Collections\ArrayCollection $locales
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
    }

    /**
     * Get the ID of the home page.
     *
     * @return int|null
     */
    public function getSiteHomePageID()
    {
        $tree = $this->getSiteTreeObject();

        return $tree === null ? null : $tree->getSiteHomePageID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeID()
     */
    public function getSiteTreeID()
    {
        $tree = $this->getSiteTreeObject();

        return $tree === null ? null : $tree->getSiteTreeID();
    }

    /**
     * Get the default locale (if set).
     *
     * @return \Concrete\Core\Entity\Site\Locale|null
     */
    public function getDefaultLocale()
    {
        foreach ($this->locales as $locale) {
            if ($locale->getIsDefault()) {
                return $locale;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeObject()
     */
    public function getSiteTreeObject()
    {
        $locale = $this->getDefaultLocale();
        if ($locale === null) {
            $locales = $this->getLocales()->toArray();
            $locale = array_shift($locales);
        }

        return $locale === null ? null : $locale->getSiteTree();
    }

    /**
     * Get the home page of the default language.
     *
     * @param string|int $version 'ACTIVE', 'RECENT' or a specific page version ID
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getSiteHomePageObject($version = 'RECENT')
    {
        $tree = $this->getSiteTreeObject();

        return $tree === null ? null : $tree->getSiteHomePageObject($version);
    }

    /**
     * Get the ID of the site.
     *
     * @return int
     */
    public function getSiteID()
    {
        return $this->siteID;
    }

    /**
     * Get the handle of the site.
     *
     * @return string
     */
    public function getSiteHandle()
    {
        return $this->siteHandle;
    }

    /**
     * Set the handle of the site.
     *
     * @param string $siteHandle
     */
    public function setSiteHandle($siteHandle)
    {
        $this->siteHandle = $siteHandle;
    }

    /**
     * Is this the default site?
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->siteIsDefault;
    }

    /**
     * Is this the default site?
     *
     * @param bool $siteIsDefault
     */
    public function setIsDefault($siteIsDefault)
    {
        $this->siteIsDefault = (bool) $siteIsDefault;
    }

    /**
     * Get the name of the site.
     *
     * @return string|mixed
     */
    public function getSiteName()
    {
        return $this->getConfigRepository()->get('name');
    }

    /**
     * Set the name of the site.
     *
     * @param string|mixed $name
     *
     * @return bool returns true if the name has been correctly set, false otherwise
     */
    public function setSiteName($name)
    {
        return $this->getConfigRepository()->save('name', $name);
    }

    /**
     * Get the main site canonical URL.
     *
     * @return string empty string if it's not set
     */
    public function getSiteCanonicalURL()
    {
        return (string) $this->getConfigRepository()->get('seo.canonical_url');
    }

    /**
     * Get the alternative site canonical URL.
     *
     * @return string empty string if it's not set
     */
    public function getSiteAlternativeCanonicalURL()
    {
        return (string) $this->getConfigRepository()->get('seo.canonical_url_alternative');
    }

    /**
     * Get the HTTPS site canonical URL (it may be the main or the alternative canonical URL).
     *
     * @return string empty string if it's not set
     */
    public function getSiteSSLCanonicalURL()
    {
        $result = '';
        $url = $this->getSiteCanonicalURL();
        if (stripos($url, 'https:') === 0) {
            $result = $url;
        } else {
            $url = $this->getSiteAlternativeCanonicalURL();
            if (stripos($url, 'https:') === 0) {
                $result = $url;
            }
        }

        return $result;
    }

    /**
     * Get the site time zone identifier.
     *
     * @return string
     */
    public function getTimezone()
    {
        $timezone = null;
        $config = $this->getConfigRepository();
        if ($config) {
            $timezone = $config->get('timezone');
        }
        if (!$timezone) {
            $timezone = date_default_timezone_get();
        }

        return $timezone;
    }

    /**
     * Get the ID of the theme.
     *
     * @return int
     */
    public function getThemeID()
    {
        return $this->pThemeID;
    }

    /**
     * Set the ID of the theme.
     *
     * @param int $pThemeID
     */
    public function setThemeID($pThemeID)
    {
        $this->pThemeID = $pThemeID;
    }
}
