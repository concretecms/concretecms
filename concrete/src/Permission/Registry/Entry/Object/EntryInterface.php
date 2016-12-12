<?php
namespace Concrete\Core\Permission\Registry\Entry\Object;

use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;

interface EntryInterface extends \Concrete\Core\Permission\Registry\Entry\EntryInterface
{

    /**
     * @param $mixed EntityInterface
     */
    function apply($mixed);


}
