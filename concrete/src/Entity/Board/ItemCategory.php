<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Tree\Node\Node;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardItemCategories")
 */
class ItemCategory
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardItemCategoryID;

    /**
     * @ORM\ManyToOne(targetEntity="Item",  inversedBy="categories")
     * @ORM\JoinColumn(name="boardItemID", referencedColumnName="boardItemID")
     **/
    protected $item;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $treeNodeID;

    public function __construct(Item $item, Node $category)
    {
        $this->item = $item;
        $this->treeNodeID = $category->getTreeNodeID();
    }

    /**
     * @return mixed
     */
    public function getBoardItemCategoryID()
    {
        return $this->boardItemCategoryID;
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
