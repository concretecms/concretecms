<?php
namespace Concrete\Core\Permission\Registry\Entry\Object;

use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;

/**
 * @since 8.0.0
 */
interface EntryInterface extends \Concrete\Core\Permission\Registry\Entry\EntryInterface
{

    /**
     * @param $mixed EntityInterface
     */
    function apply($mixed);


}
