<?php

namespace Concrete\Core\Tree\Node;

use League\Fractal\TransformerAbstract;

class NodeTransformer extends TransformerAbstract
{
    /**
     * Basic transforming of a node into an array.
     *
     * @param Node $node
     *
     * @return array
     */
    public function transform(Node $node)
    {
        return (array) $node->getTreeNodeJSON();
    }
}
