<?php
namespace Concrete\Core\Permission\Registry\Entry;

/**
 * @since 8.0.0
 */
interface EntryInterface
{

    function apply($mixed);
    function remove($mixed);

}
