<?php

namespace Concrete\Core\Tree\Menu\Item;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\ItemInterface;
use Concrete\Core\Tree\Node\Node;
use HtmlObject\Element;
use HtmlObject\Link;

abstract class AbstractNodeItem extends AbstractItem
{

    /**
     * @var $category Node
     */
    protected $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

}