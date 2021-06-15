<?php
namespace Concrete\Core\Search\Query\Modifier;

use Concrete\Core\Entity\Search\Query;
use Symfony\Component\HttpFoundation\Request;

class AutoSortColumnRequestModifier extends AbstractRequestModifier
{

    public function modify(Query $query)
    {
        $bag = $this->getParameterBag();
        $list = $this->provider->getItemList();
        if ($bag->has($list->getQuerySortColumnParameter())) {
            $sortBy = $bag->get($list->getQuerySortColumnParameter());
            $sortByDirection = $bag->get($list->getQuerySortDirectionParameter());
            if (in_array($sortBy, $list->getAutoSortColumns()) && in_array($sortByDirection, ['asc', 'desc'])) {
                // Unset all sort columns
                $columnSet = $query->getColumns();
                foreach($columnSet->getColumns() as $column) {
                    if ($column->getColumnKey() === $sortBy) {
                        $columnSet->setDefaultSortColumn($column, $sortByDirection);
                    }
                }
            }
        }

    }

}
