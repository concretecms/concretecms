<?php
namespace Concrete\Core\Entity\Site\Group;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RelationRepository")
 * @ORM\Table(name="SiteGroupRelations")
 */
class Relation
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $groupRelationID;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="group_relations")
     * @ORM\JoinColumn(name="siteGID", referencedColumnName="siteGID")
     **/
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     **/
    protected $site;


    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $gID = 0;

    /**
     * @return mixed
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

    /**
     * @return mixed
     */
    public function getInstanceGroupID()
    {
        return $this->gID;
    }

    /**
     * @param mixed $gID
     */
    public function setInstanceGroupID($gID)
    {
        $this->gID = $gID;
    }


}
