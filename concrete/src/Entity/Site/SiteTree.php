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
     * @ORM\OneToOne(targetEntity="Site", inversedBy="tree")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     **/
    protected $site;

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

    public function getSiteType()
    {
        return $this->getSite()->getType();
    }


}
