<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

use Concrete\Core\Http\Request;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\StickyRequest;

class VariableFactory
{

    protected $itemList;

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

    public function getRequestedVariables()
    {
        $variables = array();
        foreach($this->requestData as $key => $value) {
            if (strpos($key, 'ccm_offset_start_') === 0) {
                $name = substr($key, 17);
                $variables[] = new OffsetStartVariable($name, $value);
            } else if (strpos($key, 'ccm_offset_previous_') === 0) {
                $name = substr($key, 20);
                $variables[] = new PreviousOffsetStartVariable($name, $value);
            }
        }
        return $variables;
    }

}