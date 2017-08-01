<?php
namespace Concrete\Core\Search\Pagination;


interface PaginationProviderInterface
{

    /**
     * Returns the standard pagination adapter. This is used for
     * non-permissioned objects and is typically something like
     * DoctrineDbalAdapter
     * @return mixed
     */
    function getPaginationAdapter();


}
