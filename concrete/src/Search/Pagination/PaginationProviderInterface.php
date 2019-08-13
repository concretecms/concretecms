<?php
namespace Concrete\Core\Search\Pagination;


/**
 * @since 8.2.1
 */
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
