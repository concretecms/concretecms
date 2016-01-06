<?php

namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TopicsValue;


/**
 * @Entity
 * @Table(name="TopicsAttributeKeyTypes")
 */
class TopicsType extends Type
{

    /**
     * @Column(type="integer")
     */
    protected $parentNodeID = '';

    /**
     * @Column(type="integer")
     */
    protected $topicTreeID = '';

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

    public function createController()
    {
        $controller = new \Concrete\Attribute\Topics\Controller($this->getAttributeType());
        return $controller;
    }

}