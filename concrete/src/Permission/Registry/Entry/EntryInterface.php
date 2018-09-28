<?php
namespace Concrete\Core\Permission\Registry\Entry;

interface EntryInterface
{

    function apply($mixed);
    function remove($mixed);

}
