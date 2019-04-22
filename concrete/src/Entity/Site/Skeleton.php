<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Attribute\Category\SiteTypeCategory;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Attribute\Key\SiteTypeKey;
use Concrete\Core\Entity\Attribute\Value\SiteTypeValue;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="SiteSkeletons")
 */
class Skeleton implements ObjectInterface
{

    use ObjectTrait;

    public function getObjectAttributeCategory()
    {
        return \Core::make(SiteTypeCategory::class);
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = SiteTypeKey::getByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
        }

        if ($value) {
            return $value;
        } elseif ($createIfNotExists) {
            $attributeValue = new SiteTypeValue();
            $attributeValue->setSkeleton($this);
            $attributeValue->setAttributeKey($ak);
            return $attributeValue;
        }
    }

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteSkeletonID;

    /**
     * @ORM\OneToMany(targetEntity="SkeletonLocale", cascade={"all"}, mappedBy="skeleton")
     * @ORM\JoinColumn(name="siteSkeletonLocaleID", referencedColumnName="siteSkeletonLocaleID")
     **/
    protected $locales;

    /**
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\Site\Type")
     * @ORM\JoinColumn(name="siteTypeID", referencedColumnName="siteTypeID")
     */
    protected $type;

    /**
     * @return mixed
     */
    public function getSiteSkeletonID()
    {
        return $this->siteSkeletonID;
    }

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

    public function getMatchingLocale($language, $country)
    {
        foreach($this->locales as $locale) {
            if ($locale->getCountry() == $country && $locale->getLanguage() == $language) {
                return $locale;
            }
        }
    }


}
