<?php

namespace Concrete\Core\File;

use League\Fractal\TransformerAbstract;

class FileListTransformer extends TransformerAbstract
{
    public function transform(FileList $fileList)
    {
        $results = [];

        foreach ($fileList->getPagination()->getCurrentPageResults() as $file) {
            $results[] = $file->getRecentVersion()->getJSONObject();
        }

        $results = [
            'page'=> $fileList->getPagination()->getCurrentPage(),
            'total_pages'=>$fileList->getPagination()->getTotalPages(),
            'has_next_page'=>$fileList->getPagination()->hasNextPage(),
            'has_prev_page'=>$fileList->getPagination()->hasPreviousPage(),
            'results'=>$results
        ];

        return $results;

    }

}