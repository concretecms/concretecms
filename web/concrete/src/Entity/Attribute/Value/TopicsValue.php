<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="TopicAttributeValues")
 */
class TopicsValue extends Value
{

    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\SelectedTopic", mappedBy="value")
     * @JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $topics;

    /**
     * TopicsValue constructor.
     * @param $topics
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }


    public function getSelectedTopics()
    {
        return $this->topics;
    }

    public function setSelectedTopics($topics)
    {
        $this->topics = $topics;
    }
}
