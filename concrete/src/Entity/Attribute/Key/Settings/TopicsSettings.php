<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Value\Value\TopicsValue;
use Concrete\Core\Tree\Tree;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atTopicSettings")
 */
class TopicsSettings extends Settings
{
    /**
     * @ORM\Column(type="integer")
     */
    protected $akTopicParentNodeID = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $akTopicTreeID = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    protected $akTopicAllowMultipleValues = true;

    /**
     * @return mixed
     */
    public function getTopicTreeID()
    {
        return $this->akTopicTreeID;
    }

    /**
     * @param mixed $topicTreeID
     */
    public function setTopicTreeID($topicTreeID)
    {
        $this->akTopicTreeID = $topicTreeID;
    }

    /**
     * @return mixed
     */
    public function getParentNodeID()
    {
        return $this->akTopicParentNodeID;
    }

    /**
     * @param mixed $parentNodeID
     */
    public function setParentNodeID($parentNodeID)
    {
        $this->akTopicParentNodeID = $parentNodeID;
    }

    public function getTopicTreeObject()
    {
        return Tree::getByID($this->akTopicTreeID);
    }

    /**
     * @return bool
     */
    public function allowMultipleValues()
    {
        return $this->akTopicAllowMultipleValues;
    }

    /**
     * @param bool $allowMultipleValues
     */
    public function setAllowMultipleValues($allowMultipleValues)
    {
        $this->akTopicAllowMultipleValues = $allowMultipleValues;
    }

}
