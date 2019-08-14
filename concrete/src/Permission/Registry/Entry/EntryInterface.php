<?php
namespace Concrete\Core\Permission\Registry\Entry;

/**
 * @since 8.0.0
 */
interface EntryInterface
{

    function apply($mixed);
    /**
     * @since 8.2.0
     */
    function remove($mixed);

}
