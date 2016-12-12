<?php
namespace Concrete\Core\Site\Tree;

use Concrete\Core\Entity\Site\Tree;

interface TreeInterface
{

    function getSiteTreeID();

    /**
     * @return Tree
     */
    function getSiteTreeObject();


}
