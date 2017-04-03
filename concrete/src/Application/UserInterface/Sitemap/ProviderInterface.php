<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface;

interface ProviderInterface
{

    function getTreeCollection();

}
