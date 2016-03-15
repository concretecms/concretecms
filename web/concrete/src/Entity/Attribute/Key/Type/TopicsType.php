<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TopicsValue;
use Concrete\Core\Tree\Tree;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="TopicsAttributeKeyTypes")
 */
class TopicsType extends Type
{
    /**
     * @ORM\Column(type="integer")
     */
    protected $parentNodeID = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $topicTreeID = 0;

    /**
     * @return mixed
     */
    public function getTopicTreeID()
    {
        return $this->topicTreeID;
    }

    /**
     * @param mixed $topicTreeID
     */
    public function setTopicTreeID($topicTreeID)
    {
        $this->topicTreeID = $topicTreeID;
    }

    /**
     * @return mixed
     */
    public function getParentNodeID()
    {
        return $this->parentNodeID;
    }

    /**
     * @param mixed $parentNodeID
     */
    public function setParentNodeID($parentNodeID)
    {
        $this->parentNodeID = $parentNodeID;
    }

    public function getAttributeValue()
    {
        return new TopicsValue();
    }

    public function getTopicTreeObject()
    {
        return Tree::getByID($this->topicTreeID);
    }
}
