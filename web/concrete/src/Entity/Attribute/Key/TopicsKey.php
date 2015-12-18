<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\TopicsValue;


/**
 * @Entity
 * @Table(name="TopicAttributeKeys")
 */
class TopicsKey extends Key
{

    /**
     * @Column(type="integer")
     */
    protected $parentNodeID = '';

    /**
     * @Column(type="integer")
     */
    protected $topicTreeID = '';

    public function getTypeHandle()
    {
        return 'topics';
    }

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