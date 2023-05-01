<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\TreeType;

class AddTreeNodesRoutineHandler
{

    public function __invoke()
    {
        NodeType::add('page');
        NodeType::add('dashboard_package_pages');
        NodeType::add('navigation_menu');
        NodeType::add('category');
        TreeType::add('dashboard_menu');
        TreeType::add('topic');
        NodeType::add('topic');
    }


}
