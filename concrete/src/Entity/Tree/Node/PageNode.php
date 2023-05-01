<?php

namespace Concrete\Core\Entity\Tree\Node;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="TreePageNodes")
 */
class PageNode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $treeNodeID;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $cID;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $includeSubpagesInMenu = false;

    /**
     * @param mixed $treeNodeID
     */
    public function setTreeNodeID($treeNodeID): void
    {
        $this->treeNodeID = $treeNodeID;
    }

    /**
     * @return mixed
     */
    public function getTreeNodeID()
    {
        return $this->treeNodeID;
    }

    /**
     * @return bool
     */
    public function includeSubpagesInMenu(): bool
    {
        return $this->includeSubpagesInMenu;
    }

    /**
     * @param bool $includeSubpagesInMenu
     */
    public function setIncludeSubpagesInMenu(bool $includeSubpagesInMenu): void
    {
        $this->includeSubpagesInMenu = $includeSubpagesInMenu;
    }


    /**
     * @return mixed
     */
    public function getPageID()
    {
        return $this->cID;
    }

    /**
     * @param mixed $cID
     */
    public function setPageID($cID): void
    {
        $this->cID = $cID;
    }


}
