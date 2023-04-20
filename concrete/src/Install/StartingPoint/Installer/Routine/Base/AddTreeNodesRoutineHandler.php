<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\TreeType;

class AddTreeNodesRoutineHandler
{

    public function __invoke()
    {
        NodeType::add('category');
        TreeType::add('topic');
        NodeType::add('topic');
    }


}
