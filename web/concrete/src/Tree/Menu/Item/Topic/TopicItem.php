<?php

namespace Concrete\Core\Tree\Menu\Item\Topic;


use Concrete\Core\Tree\Menu\Item\AbstractItem;
use Concrete\Core\Tree\Node\Type\Topic;

abstract class TopicItem extends AbstractItem
{

    /**
     * @var $topic Topic
     */
    protected $topic;

    /**
     * CategoryItem constructor.
     * @param Category $category
     */
    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }


}