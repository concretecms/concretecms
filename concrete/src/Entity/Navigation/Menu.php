<?php

namespace Concrete\Core\Entity\Navigation;

use Concrete\Core\Menu\Type\Manager;
use Concrete\Core\Menu\Type\TypeInterface;
use Concrete\Core\Tree\Tree;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="NavigationMenus")
 */
class Menu
{


    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="integer")
     */
    protected $treeID = 0;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


    /**
     * @return int
     */
    public function getTreeID(): int
    {
        return $this->treeID;
    }

    /**
     * @param int $treeID
     */
    public function setTreeID(int $treeID): void
    {
        $this->treeID = $treeID;
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
    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getTypeDriver(): TypeInterface
    {
        return app(Manager::class)->driver($this->getType());
    }

    public function getTree(): Tree
    {
        return Tree::getByID($this->getTreeID());
    }

}
