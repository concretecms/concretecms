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
                 * @var $getNewCursor string
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
