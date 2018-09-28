<?php

namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Entity\Site\Tree;

/**
 * Interface that all the sitemap providers must implement.
 */
interface ProviderInterface
{
    /**
     * @param Tree $selectedTree
     *
     * @return \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface
     */
    public function getTreeCollection(Tree $selectedTree = null);

    /**
     * @return \stdClass[]
     */
    public function getRequestedNodes();

    /**
     * @return \Concrete\Core\Entity\Site\Tree|null
     */
    public function getRequestedSiteTree();

    /**
     * @return bool
     */
    public function includeMenuInResponse();
}
