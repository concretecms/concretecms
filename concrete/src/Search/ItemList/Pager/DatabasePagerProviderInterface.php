<?php

declare(strict_types=1);

namespace Concrete\Core\Search\ItemList\Pager;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Interface implemented by the pager providers whose items are fetched with a database query.
 */
interface DatabasePagerProviderInterface extends PagerProviderInterface
{
    /**
     * Get the query builder used to fetch the items.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQueryObject();
}
