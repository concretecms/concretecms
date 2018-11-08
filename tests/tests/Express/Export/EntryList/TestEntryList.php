<?php

namespace Concrete\Tests\Express\Export\EntryList;

use Concrete\Core\Express\EntryList;

class TestEntryList extends EntryList
{

    public function __clone()
    {
        // Don't do anything plz
    }

}