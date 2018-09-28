<?php
namespace Concrete\Core\Site;

use Concrete\Core\Entity\Site\Site;

interface SiteAggregateInterface
{

    /**
     * @return Site
     */
    function getSite();

}