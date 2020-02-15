<?php

namespace Concrete\Core\Entity\Permission;

use Concrete\Core\Entity\Site\Group\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="PermissionAccessEntitySiteGroups",
 *   indexes={
 *     @ORM\Index(name="peID", columns={"peID"}),
 *     @ORM\Index(name="siteGID", columns={"siteGID"})
 *     }
 * )
 */
class SiteGroup
{
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $pegID;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $peID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Group\Group", inversedBy="site_groups")
     * @ORM\JoinColumn(name="siteGID", referencedColumnName="siteGID")
     **/
    protected $group;

    /**
     * @return mixed
     */
    public function getSiteGroupEntityID()
    {
        return $this->pegID;
    }

    /**
     * @return mixed
     */
    public function getPermissionAccessEntityID()
    {
        return $this->peID;
    }

    /**
     * @param mixed $peID
     */
    public function setPermissionAccessEntityID($peID)
    {
        $this->peID = $peID;
    }

    /**
     * @return Group
     */
    public function getSiteGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setSiteGroup($group)
    {
        $this->group = $group;
    }
}
