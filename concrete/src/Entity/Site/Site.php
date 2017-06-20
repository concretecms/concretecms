<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Attribute\Key\SiteKey;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\SiteValue;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Site\Config\Liaison;
use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sites")
 */
class Site implements TreeInterface, ObjectInterface, PermissionObjectInterface
{
    use ObjectTrait;

    public function getPermissionObjectIdentifier()
    {
        return $this->siteID;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\SiteResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    public function getObjectAttributeCategory()
    {
        return \Core::make('\Concrete\Core\Attribute\Category\SiteCategory');
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = SiteKey::getByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
        }

        if ($value) {
            return $value;
        } elseif ($createIfNotExists) {
            $attributeValue = new SiteValue();
            $attributeValue->setSite($this);
            $attributeValue->setAttributeKey($ak);

            return $attributeValue;
        }
    }

    protected $siteConfig;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteID;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $pThemeID = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $siteIsDefault = false;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $siteHandle;

    public function __construct($appConfigRepository)
    {
        $this->updateSiteConfigRepository($appConfigRepository);
        $this->locales = new ArrayCollection();
    }

    public function updateSiteConfigRepository($appConfigRepository)
    {
        $this->siteConfig = new Liaison($appConfigRepository, $this);
    }

    public function getConfigRepository()
    {
        if (!$this->siteConfig) {
            $this->updateSiteConfigRepository(\Core::make('config'), $this);
        }

        return $this->siteConfig;
    }

    /**
     * @ORM\OneToMany(targetEntity="Locale", cascade={"all"}, mappedBy="site")
     * @ORM\JoinColumn(name="siteLocaleID", referencedColumnName="siteLocaleID")
     **/
    protected $locales;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="sites")
     * @ORM\JoinColumn(name="siteTypeID", referencedColumnName="siteTypeID")
     */
    protected $type;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * @param mixed $locales
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
    }

    /**
     * @return mixed
     */
    public function getSiteHomePageID()
    {
        $tree = $this->getSiteTreeObject();
        if (is_object($tree)) {
            return $tree->getSiteHomePageID();
        }
    }

    public function getSiteTreeID()
    {
        $tree = $this->getSiteTreeObject();
        if (is_object($tree)) {
            return $tree->getSiteTreeID();
        }
    }

    public function getDefaultLocale()
    {
        foreach ($this->locales as $locale) {
            if ($locale->getIsDefault()) {
                return $locale;
            }
        }
    }

    public function getSiteTreeObject()
    {
        $locale = $this->getDefaultLocale();
        if (!is_object($locale)) {
            $locales = $this->getLocales()->toArray();
            $locale = $locales[0];
        }

        return $locale->getSiteTree();
    }

    public function getSiteHomePageObject($version = 'RECENT')
    {
        $tree = $this->getSiteTreeObject();
        if (is_object($tree)) {
            return $tree->getSiteHomePageObject($version);
        }
    }

    /**
     * @return mixed
     */
    public function getSiteID()
    {
        return $this->siteID;
    }

    /**
     * @return mixed
     */
    public function getSiteHandle()
    {
        return $this->siteHandle;
    }

    /**
     * @param mixed $siteHandle
     */
    public function setSiteHandle($siteHandle)
    {
        $this->siteHandle = $siteHandle;
    }

    /**
     * @return mixed
     */
    public function isDefault()
    {
        return $this->siteIsDefault;
    }

    /**
     * @param mixed $siteIsDefault
     */
    public function setIsDefault($siteIsDefault)
    {
        $this->siteIsDefault = $siteIsDefault;
    }

    /**
     * @return mixed
     */
    public function getSiteName()
    {
        return $this->getConfigRepository()->get('name');
    }

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
     * @return mixed
     */
    public function getThemeID()
    {
        return $this->pThemeID;
    }

    /**
     * @param mixed $pThemeID
     */
    public function setThemeID($pThemeID)
    {
        $this->pThemeID = $pThemeID;
    }
}
