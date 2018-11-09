<?php

namespace Concrete\Tests\Express\Export\EntryList;

use League\Csv\Writer;

class TestWriter extends Writer
{

    public $headers;
    public $entries;

    public function insertOne($headers)
    {
        if ($headers instanceof \Iterator) {
            $headers = iterator_to_array($headers);
        }

        $this->headers = $headers;
    }

    public function insertAll($entries)
    {
        if ($entries instanceof \Iterator) {
            $entries = iterator_to_array($entries);
        }

        $this->entries = $entries;
    }

}