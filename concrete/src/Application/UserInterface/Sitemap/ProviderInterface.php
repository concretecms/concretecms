<?php

namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Entity\Site\Tree;

interface ProviderInterface
{
    public function getTreeCollection(Tree $selectedTree = null);

    public function getRequestedNodes();

    public function getRequestedSiteTree();

    public function includeMenuInResponse();
}
