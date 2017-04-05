<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\Site\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ProviderInterface
{

    function getTreeCollection(Tree $selectedTree = null);
    function getRequestedNodes();
    function getRequestedSiteTree();
    function includeMenuInResponse();

}
