<?php

namespace Concrete\Core\Entity\Automation;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\PackageTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="TaskSets",
 *     indexes={
 *     @ORM\Index(name="handle", columns={"handle"}),
 *     @ORM\Index(name="pkgID", columns={"pkgID"})
 *     }
 * )
 */
class TaskSet implements \JsonSerializable
{
    use PackageTrait;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Automation\TaskSetTask", mappedBy="set", cascade={"all"})
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     */
    protected $tasks;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $handle;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $displayOrder = 0;


    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getAttributeSetID();
    }

    /**
     * @return mixed
     */
    public function getTaskCollection()
    {
        return $this->tasks;
    }

    /**
     * @return Task[]
     */
    public function getTasks()
    {
        $tasks = [];
        foreach ($this->tasks as $setTask) {
            $tasks[] = $setTask->getTask();
        }

        return $tasks;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
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
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * @param mixed $displayOrder
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;
    }

    public function getDisplayName($format = 'html')
    {
        $value = tc('TaskSetName', $this->getName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getDisplayName('text'),
            'tasks' => $this->getTasks(),
        ];
    }
}
