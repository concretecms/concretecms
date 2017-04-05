<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="treeType", type="string")
 * @ORM\Table(
 *     name="SiteTreeTrees"
 * )
 */
class SiteTree extends Tree
{

    /**
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\Site\Locale", inversedBy="tree")
     * @ORM\JoinColumn(name="siteLocaleID", referencedColumnName="siteLocaleID")
     **/
    protected $locale;

    /**
     * @return Locale
     */
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

    public function getSite()
    {
        return $this->getLocale()->getSite();
    }


    public function getSiteType()
    {
        return $this->getLocale()->getSite()->getType();
    }

    public function getDisplayName()
    {
        return $this->getLocale()->getSite()->getSiteName();
    }

}
