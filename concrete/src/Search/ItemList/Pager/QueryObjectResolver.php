<?php

declare(strict_types=1);

namespace Concrete\Core\Search\ItemList\Pager;

use Doctrine\DBAL\Query\QueryBuilder;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Resolve the query builder of the pager providers.
 */
final class QueryObjectResolver
{
    /**
     * Get the query builder used by a pager provider to fetch its items.
     *
     * @throws \InvalidArgumentException if the pager provider doesn't have a query object
     */
    public static function getQueryObject(PagerProviderInterface $itemList): QueryBuilder
    {
        // Pager providers that don't implement DatabasePagerProviderInterface may still provide the query object
        if (!$itemList instanceof DatabasePagerProviderInterface && !method_exists($itemList, 'getQueryObject')) {
            throw new \InvalidArgumentException(t('The pager provider must have a query object.'));
        }

        return $itemList->getQueryObject();
    }
}
