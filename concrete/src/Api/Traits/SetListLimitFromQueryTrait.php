<?php

namespace Concrete\Core\Api\Traits;

use Concrete\Core\Legacy\ItemList as LegacyItemList;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Api\Exception\InvalidLimitQueryParameterValueException;
use Symfony\Component\HttpFoundation\Request;

trait SetListLimitFromQueryTrait
{

    public function addLimitToPaginationIfSpecified($pagination, Request $request)
    {
        if ($request->query->has('limit')) {
            $limit = $request->query->get('limit');
            $numbers = new Numbers();
            if ($numbers->integer($limit)) {
                if ($limit < 1 || $limit > 100) {
                    throw new InvalidLimitQueryParameterValueException();
                }
            } else {
                throw new InvalidLimitQueryParameterValueException();
            }
        }
        if (!isset($limit)) {
            $limit = 10;
        }

        if ($pagination instanceof PagerPagination) {
            $pagination->setMaxPerPage($limit);
        } else if ($pagination instanceof LegacyItemList) {
            $pagination->setItemsPerPage($limit);
        }
    }

}
