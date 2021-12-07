<?php
namespace Concrete\Core\File\Component\Chooser;

use Symfony\Component\HttpFoundation\Request;

class FilterCollectionFactory
{

    public function createFromRequest(Request $request): ?FilterCollectionInterface
    {
        $collection = new FilterCollection();
        if ($request->query->has('filters')) {
            $filters = (array) $request->query->get('filters');
            foreach ($filters as $filterEntry) {
                $filter = null;
                if (isset($filterEntry['field']) && $filterEntry['field'] == 'type') {
                    $filter = new FileTypeFilter((int) $filterEntry['type']);
                }
                if (isset($filterEntry['field']) && $filterEntry['field'] == 'extension') {
                    $filter = new FileExtensionFilter((array) $filterEntry['extension']);
                }

                if ($filter) {
                    $collection->addFilter($filter);
                }
            }
        }

        return $collection;
    }

    
}