<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="TopicAttributeValues")
 */
class TopicsValue extends Value implements \ArrayAccess
{
    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\SelectedTopic", mappedBy="value", cascade={"all"})
     * @JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $topics;

    /**
     * TopicsValue constructor.
     *
     * @param $topics
     */
    public function __construct()
    {
        parent::__construct();
        $this->topics = new ArrayCollection();
    }

    public function offsetSet($offset, $value)
    {
        $this->topics->offsetSet($offset, $value);
    }

    public function offsetExists($offset)
    {
        return $this->topics->offsetExists($offset);
    }

    public function offsetUnset($offset)
    {
        $this->offsetUnset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->offsetGet($offset);
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
