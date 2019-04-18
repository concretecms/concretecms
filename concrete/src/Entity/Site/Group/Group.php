<?php
namespace Concrete\Core\Entity\Site\Group;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="GroupRepository")
 * @ORM\Table(name="SiteGroups")
 */
class Group
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteGID;

    /**
     * @ORM\OneToMany(targetEntity="Relation", cascade={"all"}, mappedBy="group")
     **/
    protected $group_relations;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Permission\SiteGroup", cascade={"all"}, mappedBy="group")
     **/
    protected $site_groups;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Type")
     * @ORM\JoinColumn(name="siteTypeID", referencedColumnName="siteTypeID")
     **/
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $groupName;

    /**
     * @param $group_relations
     */
    public function __construct()
    {
        $this->group_relations = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSiteGroupID()
    {
        return $this->siteGID;
    }

    /**
     * @return ArrayCollection
     */
    public function getSiteGroupRelations()
    {
        return $this->group_relations;
    }

    /**
     * @return mixed
     */
    public function getSiteGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param mixed $groupName
     */
    public function setSiteGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

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
    public function setSiteType($type)
    {
        $this->type = $type;
    }


}
