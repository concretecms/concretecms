<?php

namespace Concrete\Core\Api\Traits;

use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\ItemList;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\ResourceAbstract;
use Symfony\Component\HttpFoundation\Request;

trait SupportsCursorTrait
{

    public function getCurrentCursorFromRequest(Request $request)
    {
        return $this->request->query->get('after', null);
    }

    /**
     * Add the pagination cursor to a resource collection.
     *
     * @param iterable $results the items of the current page
     * @param string|callable $getNewCursor the name of the item method returning the cursor of an item, or a callable receiving the item and returning it
     * @param \League\Fractal\Resource\Collection $resource
     * @param string|int|null $previousCursor
     */
    public function addCursorToResource(
        iterable $results,
        Request $request,
        $getNewCursor,
        ResourceAbstract $resource,
        $previousCursor = null
    ) {
        if (count($results) > 0) {
            if (is_callable($getNewCursor)) {
                $newCursor = $getNewCursor(collect($results)->last());
            } else {
                /**
                 * @var string $getNewCursor
                 */
                $newCursor = collect($results)->last()->$getNewCursor();
            }
        } else {
            $newCursor = null;
        }

        $cursor = new Cursor(
            $this->getCurrentCursorFromRequest($request), $previousCursor, $newCursor, count($results)
        );
        $resource->setCursor($cursor);
        return $resource;
    }

    /**
     * @param \Concrete\Core\Search\ItemList\ItemList&\Concrete\Core\Search\ItemList\Pager\PagerProviderInterface $list
     * @param \Concrete\Core\Search\Column\Column&\Concrete\Core\Search\Column\PagerColumnInterface $column
     */
    public function setupSortAndCursor(
        Request $request,
        ItemList $list,
        PagerColumnInterface $column,
        callable $getCursorObjectFunction
    ) {
        $currentCursor = $this->getCurrentCursorFromRequest($request);
        $list->sortBySearchColumn($column);
        if ($currentCursor) {
            $object = $getCursorObjectFunction($currentCursor);
            if ($object) {
                $column->filterListAtOffset($list, $object);
            }
        }
    }
}
