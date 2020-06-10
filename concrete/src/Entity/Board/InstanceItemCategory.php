<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Tree\Node\Node;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstanceItemCategories")
 */
class InstanceItemCategory
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardInstanceItemCategoryID;

    /**
     * @ORM\ManyToOne(targetEntity="InstanceItem",  inversedBy="categories")
     * @ORM\JoinColumn(name="boardInstanceItemID", referencedColumnName="boardInstanceItemID")
     **/
    protected $item;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $treeNodeID;

    public function __construct(InstanceItem $item, Node $category)
    {
        $this->item = $item;
        $this->treeNodeID = $category->getTreeNodeID();
    }

    /**
     * @return mixed
     */
    public function getBoardInstanceItemCategoryID()
    {
        return $this->boardInstanceItemCategoryID;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item): void
    {
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getTreeNodeID()
    {
        return $this->treeNodeID;
    }

    /**
     * @param mixed $treeNodeID
     */
    public function setTreeNodeID($treeNodeID): void
    {
        $this->treeNodeID = $treeNodeID;
    }








}
