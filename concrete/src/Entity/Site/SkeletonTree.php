<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteSkeletonTrees")
 */
class SkeletonTree extends Tree
{

    /**
     * @ORM\OneToOne(targetEntity="SkeletonLocale", inversedBy="tree")
     * @ORM\JoinColumn(name="skeletonLocaleID", referencedColumnName="skeletonLocaleID")
     **/
    protected $locale;

    /**
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\Site\Type")
     * @ORM\JoinColumn(name="siteTypeID", referencedColumnName="siteTypeID")
     */
    protected $type;

    /**
     * @return mixed
     */
    public function getSiteType()
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


    public function getDisplayName()
    {
        return t('Skeleton > %s', $this->getSiteType()->getSiteTypeName());
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }


}
