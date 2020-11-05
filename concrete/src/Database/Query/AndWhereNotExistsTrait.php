<?php
namespace Concrete\Core\Database\Query;

use Doctrine\DBAL\Query\QueryBuilder;

trait AndWhereNotExistsTrait
{

    public function andWhereNotExists(QueryBuilder $query, $expr)
    {
        $where = (string) $query->getQueryPart('where');
        // This is not an elegant way to do this, but doctrine dbal's querybuilder won't let me inspect
        // the parts, so I have to do with a string search.
        // What's the purpose of this code, you ask? Our pager manager calls `filterListAtOffset` several times
        // when building pages, and potentially many times if the list the user is looking contains many items
        // that they don't have the ability to see. The permissioned pager pagination skips over those items, and
        // continues down the result set to find more items to fill the page. This means that `filterListAtOffset`
        // could be called many times. If we only have `andWhere` called, we may be stacking up tens or hundreds of
        // pager pagination cursor next query parts at the end, which could overwhelm mysql. They look like
        // "AND (e.exEntryDateCreated, e.exEntryID) < (foo, bar)" stacked up many times at the end of the queries.
        // Instead, let's only add this query part to the query if it isn't already there. This should be fine
        // because the parameters are updated in the child classes.
        if (strpos($where, $expr) === false) {
            $query->andWhere($expr);
        }
    }

}
