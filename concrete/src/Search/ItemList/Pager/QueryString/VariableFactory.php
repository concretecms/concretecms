<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

use Concrete\Core\Http\Request;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Search\StickyRequest;

class VariableFactory
{

    protected $itemList;

    public function getCursorVariableName()
    {
        return 'ccm_cursor';
    }

    /**
     * VariableFactory constructor.
     * @param $itemList
     */
    public function __construct(PagerProviderInterface $itemList, StickyRequest $request = null)
    {
        $this->itemList = $itemList;
        if ($request) {
            $this->requestData = $request->getSearchRequest();
        } else {
            $request = Request::createFromGlobals();
            $this->requestData = $request->query->all();
        }
    }

    public function getCursorValue()
    {
        if (isset($this->requestData[$this->getCursorVariableName()])) {
            return $this->requestData[$this->getCursorVariableName()];
        }
    }

    public function getCurrentCursor()
    {
        $cursor = explode('|', $this->getCursorValue());
        return end($cursor);
    }

    public function getNextCursorValue(PagerPagination $pagination)
    {
        $currentCursor = $this->getCursorValue();
        $nextCursor = $this->itemList->getPagerManager()->getNextCursorStart($this->itemList, $pagination);
        if ($currentCursor) {
            return sprintf('%s|%s', $currentCursor, $nextCursor);
        }
        return $nextCursor;
    }

    public function getPreviousCursorValue(PagerPagination $pagination)
    {
        $cursor = explode('|', $this->getCursorValue());
        array_pop($cursor);
        return implode('|', $cursor);
    }



}