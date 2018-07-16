<?php

namespace Concrete\Core\Site\Tree;

interface TreeInterface
{
    /**
     * @return int|null
     */
    public function getSiteTreeID();

    /**
     * @return \Concrete\Core\Entity\Site\Tree|null
     */
    public function getSiteTreeObject();
}
