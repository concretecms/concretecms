<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Key\SiteKey;
use Concrete\Core\Entity\Attribute\Value\SiteValue;
use Concrete\Core\Site\Config\Liaison;
use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sites")
 */
class Site implements TreeInterface
{

    use ObjectTrait;

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
    }

    public function updateSiteConfigRepository($appConfigRepository)
    {
        $this->siteConfig = new Liaison($appConfigRepository, $this);
    }

    public function getConfigRepository()
    {
        return $this->siteConfig;
    }

    /**
     * @ORM\OneToOne(targetEntity="SiteTree", cascade={"all"}, mappedBy="site")
     * @ORM\JoinColumn(name="siteTreeID", referencedColumnName="siteTreeID")
     **/
    protected $tree;

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
    public function getSiteHomePageID()
    {
        return $this->tree->getSiteHomePageID();
    }

    public function getSiteTreeID()
    {
        if (is_object($this->tree)) {
            return $this->tree->getSiteTreeID();
        }
    }

    public function getSiteTreeObject()
    {
        if (is_object($this->tree)) {
            return $this->tree;
        }
    }

    public function getSiteHomePageObject()
    {
        if (is_object($this->tree)) {
            return \Page::getByID($this->tree->getSiteHomePageID());
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
    public function getSiteTree()
    {
        return $this->tree;
    }

    /**
     * @param mixed $tree
     */
    public function setSiteTree($tree)
    {
        $this->tree = $tree;
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

    public function getSiteCanonicalURL()
    {
        return $this->getConfigRepository()->get('seo.canonical_url');
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
