<?php

namespace Concrete\Core\Site\Tree;

/**
 * @since 8.0.0
 */
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
