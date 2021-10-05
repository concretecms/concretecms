<?php

namespace Concrete\Tests\Express\Export\EntryList;

use League\Csv\Writer;

class TestWriter extends Writer
{

    public $headers;
    public $entries;

    public function insertOne(iterable $headers): int
    {
        if ($headers instanceof \Iterator) {
            $headers = iterator_to_array($headers);
        }

        $this->headers = $headers;

        return 1;
    }

    public function insertAll(iterable $entries):int
    {
        if ($entries instanceof \Iterator) {
            $entries = iterator_to_array($entries);
        }

        $this->entries = $entries;

        return count($entries);
    }

}