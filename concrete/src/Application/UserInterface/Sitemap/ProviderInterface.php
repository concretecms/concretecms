<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface;
use Concrete\Core\Entity\Site\SiteTree;

interface ProviderInterface
{

    function getTreeCollection(SiteTree $selectedTree = null);

}
