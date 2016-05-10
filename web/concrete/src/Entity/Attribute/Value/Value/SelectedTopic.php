<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

/**
 * @Entity
 * @Table(name="TopicAttributeSelectedTopics")
 */
class SelectedTopic
{
    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avTreeTopicNodeID;

    /**
     * @ManyToOne(targetEntity="TopicsValue")
     * @JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $value;

    /**
     * @Column(type="integer", options={"unsigned":true})
     */
    protected $treeNodeID;

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
    public function setTreeNodeID($treeNodeID)
    {
        $this->treeNodeID = $treeNodeID;
    }

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setAttributeValue($value)
    {
        $this->value = $value;
    }
}
