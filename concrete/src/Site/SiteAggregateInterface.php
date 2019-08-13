<?php
namespace Concrete\Core\Site;

use Concrete\Core\Entity\Site\Site;

/**
 * @since 8.2.0
 */
interface SiteAggregateInterface
{

    /**
     * @return Site
     */
    function getSite();

}