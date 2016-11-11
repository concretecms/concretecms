<?php
namespace Concrete\Core\Permission\Registry\Entry;

use Concrete\Core\Permission\AssignableObjectInterface;

interface EntryInterface
{

    function apply(AssignableObjectInterface $object);

}
