<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Site\Tree\TreeInterface;

interface LocaleEntityInterface
{

    /**
     * @return TreeInterface
     */
    public function getSiteTree();

    /**
     * @param TreeInterface $tree
     */
    public function setSiteTree($tree);

}
