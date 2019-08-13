<?php
namespace Concrete\Core\Permission\Registry\Entry\Access;

use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

/**
 * @since 8.0.0
 */
interface EntryInterface extends \Concrete\Core\Permission\Registry\Entry\EntryInterface
{

    /**
     * @param $mixed ObjectInterface
     */
    function apply($mixed);


}

